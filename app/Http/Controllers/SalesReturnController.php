<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SaleDetail;
use App\Models\SaleMaster;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Stock;
use App\Support\RolePermissionAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SalesReturnController extends Controller
{
    private const MODULE_NAMES = ['sales-return', 'return-sales'];

    public function index(Request $request): View
    {
        $access = app(RolePermissionAccess::class);
        $canView = $this->can($access, 'view');
        $canAdd = $this->can($access, 'add');

        abort_unless($canView || $canAdd, 403);

        $customers = Customer::orderBy('customer_name')->get(['customer_id', 'customer_name']);
        $selectedCustomerId = (string) $request->input('customer_id', '');
        $selectedSaleId = (string) $request->input('sale_id', '');

        $sales = collect();
        $sale = null;
        $details = collect();

        if ($selectedCustomerId !== '') {
            $sales = SaleMaster::query()
                ->where('customer_id', (int) $selectedCustomerId)
                ->orderBy('sale_date', 'desc')
                ->orderBy('id', 'desc')
                ->get(['id', 'sale_date', 'invoice_no']);
        }

        if ($selectedCustomerId !== '' && $selectedSaleId !== '') {
            $sale = SaleMaster::query()
                ->where('customer_id', (int) $selectedCustomerId)
                ->find((int) $selectedSaleId);

            if ($sale) {
                $details = SaleDetail::query()
                    ->where('sale_id', $sale->id)
                    ->withSum('returnItems as returned_qty', 'return_qty')
                    ->get()
                    ->map(function (SaleDetail $detail) {
                        $returnedQty = round((float) ($detail->returned_qty ?? 0), 3);
                        $saleQty = round((float) $detail->qty, 3);
                        $maxQty = max(0, round($saleQty - $returnedQty, 3));
                        $rate = round((float) $detail->rate, 2);

                        $detail->returned_qty = $returnedQty;
                        $detail->max_return_qty = $maxQty;
                        $detail->return_rate = $rate;

                        return $detail;
                    });
            }
        }

        return view('sales_returns.index', [
            'customers' => $customers,
            'sales' => $sales,
            'sale' => $sale,
            'details' => $details,
            'canAdd' => $canAdd,
            'filters' => [
                'customer_id' => $selectedCustomerId,
                'sale_id' => $selectedSaleId,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'add'), 403);

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,customer_id'],
            'sale_id' => ['required', 'exists:sales_master,id'],
            'return_date' => ['required', 'date'],
            'sale_detail_id' => ['required', 'array', 'min:1'],
            'sale_detail_id.*' => ['required', 'exists:sales_details,id'],
            'return_qty' => ['required', 'array', 'min:1'],
            'return_qty.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated): void {
            $sale = SaleMaster::query()
                ->lockForUpdate()
                ->findOrFail((int) $validated['sale_id']);

            if ((int) ($sale->customer_id ?? 0) !== (int) $validated['customer_id']) {
                throw ValidationException::withMessages([
                    'sale_id' => 'Selected invoice does not belong to selected customer.',
                ]);
            }

            $detailIds = array_map('intval', $validated['sale_detail_id']);
            $details = SaleDetail::query()
                ->where('sale_id', $sale->id)
                ->whereIn('id', $detailIds)
                ->withSum('returnItems as returned_qty', 'return_qty')
                ->get()
                ->keyBy('id');

            $rows = [];
            $totalReturn = 0.0;

            foreach ($validated['sale_detail_id'] as $idx => $detailIdRaw) {
                $detailId = (int) $detailIdRaw;
                $detail = $details->get($detailId);
                if (! $detail) {
                    throw ValidationException::withMessages([
                        'sale_detail_id.' . $idx => 'Invalid sales item selected.',
                    ]);
                }

                $returnQty = round((float) ($validated['return_qty'][$idx] ?? 0), 3);
                if ($returnQty <= 0) {
                    continue;
                }

                $alreadyReturned = round((float) ($detail->returned_qty ?? 0), 3);
                $saleQty = round((float) $detail->qty, 3);
                $maxQty = max(0, round($saleQty - $alreadyReturned, 3));
                if ($returnQty > $maxQty) {
                    throw ValidationException::withMessages([
                        'return_qty.' . $idx => 'Return qty cannot exceed available qty (' . number_format($maxQty, 3) . ').',
                    ]);
                }

                $rate = round((float) $detail->rate, 2);
                $amount = round($returnQty * $rate, 2);
                $totalReturn += $amount;

                $rows[] = [
                    'sale_detail_id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'item_code' => $detail->item_code,
                    'product_name' => $detail->product_name,
                    'uom' => $detail->uom,
                    'sale_qty' => $saleQty,
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

            $returnNo = $this->nextReturnNumber();
            $salesReturn = SalesReturn::create([
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'customer_name' => $sale->customer_name,
                'sale_invoice_no' => $sale->invoice_no,
                'return_no' => $returnNo,
                'return_date' => $validated['return_date'],
                'total_return_amount' => round($totalReturn, 2),
            ]);

            foreach ($rows as $row) {
                SalesReturnItem::create([
                    'sales_return_id' => $salesReturn->id,
                    'sale_detail_id' => $row['sale_detail_id'],
                    'product_id' => $row['product_id'],
                    'item_code' => $row['item_code'],
                    'product_name' => $row['product_name'],
                    'uom' => $row['uom'],
                    'sale_qty' => $row['sale_qty'],
                    'return_qty' => $row['return_qty'],
                    'rate' => $row['rate'],
                    'amount' => $row['amount'],
                ]);

                Stock::create([
                    'purchase_id' => null,
                    'sale_id' => $sale->id,
                    'entry_date' => $validated['return_date'],
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'uom' => $row['uom'],
                    'qty' => (float) $row['return_qty'],
                    'supplier_id' => null,
                    'batch_id' => 'SR-' . $returnNo,
                ]);
            }
        });

        return redirect()
            ->route('sales-returns.index', [
                'customer_id' => $validated['customer_id'],
                'sale_id' => $validated['sale_id'],
            ])
            ->with('success', 'Sales return saved successfully.');
    }

    private function nextReturnNumber(): string
    {
        $next = ((int) SalesReturn::max('id')) + 1;

        return 'SR-' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
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
