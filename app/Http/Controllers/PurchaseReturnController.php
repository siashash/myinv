<?php

namespace App\Http\Controllers;

use App\Models\PurchaseDetail;
use App\Models\PurchaseMaster;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\SupplierCreditNote;
use App\Support\RolePermissionAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseReturnController extends Controller
{
    private const MODULE_NAMES = ['purchase-return', 'return-purchase'];

    public function index(Request $request): View
    {
        $access = app(RolePermissionAccess::class);
        $canView = $this->can($access, 'view');
        $canAdd = $this->can($access, 'add');

        abort_unless($canView || $canAdd, 403);

        $suppliers = Supplier::orderBy('supplier_name')->get(['supplier_id', 'supplier_name']);
        $selectedSupplierId = (string) $request->input('supplier_id', '');
        $selectedPurchaseId = (string) $request->input('purchase_id', '');

        $purchases = collect();
        $purchase = null;
        $details = collect();

        if ($selectedSupplierId !== '') {
            $purchases = PurchaseMaster::query()
                ->where('supplier_id', (int) $selectedSupplierId)
                ->orderBy('purchase_date', 'desc')
                ->orderBy('id', 'desc')
                ->get(['id', 'purchase_date', 'supplier_inv_no']);
        }

        if ($selectedSupplierId !== '' && $selectedPurchaseId !== '') {
            $purchase = PurchaseMaster::query()
                ->where('supplier_id', (int) $selectedSupplierId)
                ->find((int) $selectedPurchaseId);

            if ($purchase) {
                $details = PurchaseDetail::query()
                    ->where('pur_id', $purchase->id)
                    ->withSum('returnItems as returned_qty', 'return_qty')
                    ->get()
                    ->map(function (PurchaseDetail $detail) {
                        $returnedQty = round((float) ($detail->returned_qty ?? 0), 3);
                        $purchaseQty = round((float) $detail->qty, 3);
                        $maxQty = max(0, round($purchaseQty - $returnedQty, 3));
                        $rate = $purchaseQty > 0 ? round((float) $detail->net_amount / $purchaseQty, 4) : 0.0;

                        $detail->returned_qty = $returnedQty;
                        $detail->max_return_qty = $maxQty;
                        $detail->return_rate = $rate;

                        return $detail;
                    });
            }
        }

        return view('purchase_returns.index', [
            'suppliers' => $suppliers,
            'purchases' => $purchases,
            'purchase' => $purchase,
            'details' => $details,
            'canAdd' => $canAdd,
            'filters' => [
                'supplier_id' => $selectedSupplierId,
                'purchase_id' => $selectedPurchaseId,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'add'), 403);

        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,supplier_id'],
            'purchase_id' => ['required', 'exists:purchase_master,id'],
            'return_date' => ['required', 'date'],
            'purchase_detail_id' => ['required', 'array', 'min:1'],
            'purchase_detail_id.*' => ['required', 'exists:purchase_details,id'],
            'return_qty' => ['required', 'array', 'min:1'],
            'return_qty.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated): void {
            $purchase = PurchaseMaster::query()
                ->lockForUpdate()
                ->findOrFail((int) $validated['purchase_id']);

            if ((int) $purchase->supplier_id !== (int) $validated['supplier_id']) {
                throw ValidationException::withMessages([
                    'purchase_id' => 'Selected invoice does not belong to selected supplier.',
                ]);
            }

            $detailIds = array_map('intval', $validated['purchase_detail_id']);
            $details = PurchaseDetail::query()
                ->where('pur_id', $purchase->id)
                ->whereIn('id', $detailIds)
                ->withSum('returnItems as returned_qty', 'return_qty')
                ->get()
                ->keyBy('id');

            $rows = [];
            $totalCredit = 0.0;

            foreach ($validated['purchase_detail_id'] as $idx => $detailIdRaw) {
                $detailId = (int) $detailIdRaw;
                $detail = $details->get($detailId);
                if (! $detail) {
                    throw ValidationException::withMessages([
                        'purchase_detail_id.' . $idx => 'Invalid purchase item selected.',
                    ]);
                }

                $returnQty = round((float) ($validated['return_qty'][$idx] ?? 0), 3);
                if ($returnQty <= 0) {
                    continue;
                }

                $alreadyReturned = round((float) ($detail->returned_qty ?? 0), 3);
                $purchaseQty = round((float) $detail->qty, 3);
                $maxQty = max(0, round($purchaseQty - $alreadyReturned, 3));
                if ($returnQty > $maxQty) {
                    throw ValidationException::withMessages([
                        'return_qty.' . $idx => 'Return qty cannot exceed available qty (' . number_format($maxQty, 3) . ').',
                    ]);
                }

                $rate = $purchaseQty > 0 ? round((float) $detail->net_amount / $purchaseQty, 4) : 0.0;
                $amount = round($returnQty * $rate, 2);
                $totalCredit += $amount;

                $rows[] = [
                    'purchase_detail_id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'product_name' => $detail->product_name,
                    'uom' => $detail->sales_unit,
                    'purchase_qty' => $purchaseQty,
                    'return_qty' => $returnQty,
                    'rate' => $rate,
                    'amount' => $amount,
                ];
            }

            if (count($rows) === 0) {
                throw ValidationException::withMessages([
                    'return_qty' => 'Please enter return qty for at least one row.',
                ]);
            }

            $creditNoteNo = $this->generateCreditNoteNo((string) $purchase->supplier_name, (string) $validated['return_date']);

            $purchaseReturn = PurchaseReturn::create([
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'supplier_name' => $purchase->supplier_name,
                'supplier_inv_no' => $purchase->supplier_inv_no,
                'credit_note_no' => $creditNoteNo,
                'return_date' => $validated['return_date'],
                'total_credit_amount' => round($totalCredit, 2),
            ]);

            foreach ($rows as $row) {
                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_detail_id' => $row['purchase_detail_id'],
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'uom' => $row['uom'],
                    'purchase_qty' => $row['purchase_qty'],
                    'return_qty' => $row['return_qty'],
                    'rate' => $row['rate'],
                    'amount' => $row['amount'],
                ]);

                Stock::create([
                    'purchase_id' => $purchase->id,
                    'entry_date' => $validated['return_date'],
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'uom' => $row['uom'],
                    'qty' => -1 * $row['return_qty'],
                    'supplier_id' => $purchase->supplier_id,
                    'batch_id' => $this->buildReturnBatchId($creditNoteNo),
                ]);
            }

            SupplierCreditNote::create([
                'purchase_return_id' => $purchaseReturn->id,
                'supplier_id' => $purchase->supplier_id,
                'purchase_id' => $purchase->id,
                'credit_note_no' => $creditNoteNo,
                'credit_amount' => round($totalCredit, 2),
                'remaining_amount' => round($totalCredit, 2),
                'note_date' => $validated['return_date'],
            ]);
        });

        return redirect()
            ->route('purchase-returns.index', [
                'supplier_id' => $validated['supplier_id'],
                'purchase_id' => $validated['purchase_id'],
            ])
            ->with('success', 'Purchase return saved and credit note created.');
    }

    private function generateCreditNoteNo(string $supplierName, string $returnDate): string
    {
        $letters = preg_replace('/[^A-Za-z]/', '', $supplierName) ?? '';
        $prefix = strtoupper(substr($letters, 0, 2));
        if (strlen($prefix) < 2) {
            $prefix = str_pad($prefix, 2, 'X');
        }

        $datePart = strtoupper(date('M-y', strtotime($returnDate)));

        return 'CN-' . $prefix . '-' . $datePart . '-' . strtoupper(substr(uniqid('', true), -4));
    }

    private function buildReturnBatchId(string $creditNoteNo): string
    {
        return 'RET-' . strtoupper($creditNoteNo);
    }

    private function can(RolePermissionAccess $access, string $action): bool
    {
        foreach (self::MODULE_NAMES as $moduleName) {
            if ($access->allows($moduleName, $action)) {
                return true;
            }
        }

        return false;
    }
}
