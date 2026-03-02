<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleMaster;
use App\Models\Stock;
use App\Support\RolePermissionAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesController extends Controller
{
    private const MODULE_NAMES = ['sales', 'transaction-sales'];

    public function index()
    {
        $access = app(RolePermissionAccess::class);
        $canView = $this->can($access, 'view');
        $canAdd = $this->can($access, 'add');
        $canEdit = $this->can($access, 'edit');
        $canDelete = $this->can($access, 'delete');

        abort_unless($canView || $canAdd || $canEdit || $canDelete, 403);

        $customers = Customer::orderBy('customer_name')->get(['customer_id', 'customer_name']);
        $products = Product::orderBy('product_name')->get([
            'id',
            'product_name',
            'product_code',
            'uom',
            'sales_uom',
            'sales_price_bu',
            'sales_price_su',
            'sale_price',
            'cgst_percent',
            'sgst_percent',
            'igst_percent',
        ]);
        $sales = SaleMaster::with('customer')->orderBy('id', 'desc')->get();
        $nextInvoiceNo = $this->nextInvoiceNumber();

        return view('sales.index', compact('customers', 'products', 'sales', 'nextInvoiceNo', 'canAdd', 'canEdit', 'canDelete'));
    }

    public function store(Request $request)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'add'), 403);

        $validated = $request->validate([
            'sale_date' => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_id' => ['nullable', 'exists:customers,customer_id'],
            'sale_mode' => ['required', 'in:Cash,Credit,UPI'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'exists:products,id'],
            'unit_name' => ['required', 'array', 'min:1'],
            'unit_name.*' => ['required', 'in:uom,sales_uom'],
            'qty' => ['required', 'array', 'min:1'],
            'qty.*' => ['required', 'numeric', 'gt:0'],
        ]);

        DB::transaction(function () use ($validated): void {
            $detailsPayload = $this->buildDetailsPayload($validated, null);

            $sale = SaleMaster::create([
                'sale_date' => $validated['sale_date'],
                'invoice_no' => $this->nextInvoiceNumber(),
                'customer_id' => $validated['customer_id'] ?: null,
                'customer_name' => $validated['customer_name'],
                'sale_mode' => $validated['sale_mode'],
                'discount_amount' => $detailsPayload['discount_amount'],
                'total_amount' => $detailsPayload['total_amount'],
            ]);

            foreach ($detailsPayload['details'] as $detail) {
                $sale->details()->create($detail);
            }

            $this->syncStockForSale($sale, $detailsPayload['details'], $validated['sale_date']);
        });

        return redirect()->route('sales.index')->with('success', 'Sale saved successfully.');
    }

    public function edit(SaleMaster $sale)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        $sale->load('details');
        $customers = Customer::orderBy('customer_name')->get(['customer_id', 'customer_name']);
        $products = Product::orderBy('product_name')->get([
            'id',
            'product_name',
            'product_code',
            'uom',
            'sales_uom',
            'sales_price_bu',
            'sales_price_su',
            'sale_price',
            'cgst_percent',
            'sgst_percent',
            'igst_percent',
        ]);

        return view('sales.edit', compact('sale', 'customers', 'products'));
    }

    public function update(Request $request, SaleMaster $sale)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        $validated = $request->validate([
            'sale_date' => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_id' => ['nullable', 'exists:customers,customer_id'],
            'sale_mode' => ['required', 'in:Cash,Credit,UPI'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'exists:products,id'],
            'unit_name' => ['required', 'array', 'min:1'],
            'unit_name.*' => ['required', 'in:uom,sales_uom'],
            'qty' => ['required', 'array', 'min:1'],
            'qty.*' => ['required', 'numeric', 'gt:0'],
        ]);

        DB::transaction(function () use ($validated, $sale): void {
            $detailsPayload = $this->buildDetailsPayload($validated, (int) $sale->id);

            $sale->update([
                'sale_date' => $validated['sale_date'],
                'customer_id' => $validated['customer_id'] ?: null,
                'customer_name' => $validated['customer_name'],
                'sale_mode' => $validated['sale_mode'],
                'discount_amount' => $detailsPayload['discount_amount'],
                'total_amount' => $detailsPayload['total_amount'],
            ]);

            $sale->details()->delete();
            foreach ($detailsPayload['details'] as $detail) {
                $sale->details()->create($detail);
            }

            $this->syncStockForSale($sale, $detailsPayload['details'], $validated['sale_date']);
        });

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    public function destroy(SaleMaster $sale)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'delete'), 403);

        Stock::query()->where('sale_id', $sale->id)->delete();
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }

    private function nextInvoiceNumber(): string
    {
        $next = ((int) SaleMaster::max('id')) + 1;

        return 'SI-' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    private function buildDetailsPayload(array $validated, ?int $excludeSaleId = null): array
    {
        $productIds = $validated['product_id'];
        $qtyList = $validated['qty'];
        $unitNameList = $validated['unit_name'];

        $count = count($productIds);
        if ($count !== count($qtyList) || $count !== count($unitNameList)) {
            throw ValidationException::withMessages([
                'product_id' => 'Sales detail rows are invalid.',
            ]);
        }

        $products = Product::whereIn('id', $productIds)
            ->get([
                'id',
                'product_name',
                'product_code',
                'uom',
                'sales_uom',
                'sales_price_bu',
                'sales_price_su',
                'sale_price',
                'cgst_percent',
                'sgst_percent',
                'igst_percent',
            ])
            ->keyBy('id');

        $details = [];
        $invoiceAmount = 0;
        $availableByProduct = [];

        for ($i = 0; $i < $count; $i++) {
            $productId = (int) $productIds[$i];
            $product = $products->get($productId);
            if (! $product) {
                throw ValidationException::withMessages([
                    'product_id.' . $i => 'Selected product does not exist.',
                ]);
            }

            $unitName = (string) $unitNameList[$i];
            $qty = round((float) $qtyList[$i], 3);
            $resolvedUom = $unitName === 'sales_uom' ? (string) ($product->sales_uom ?: $product->uom) : (string) $product->uom;

            if (! array_key_exists($productId, $availableByProduct)) {
                $stockQuery = Stock::query()
                    ->where('product_id', $productId);

                if ($excludeSaleId) {
                    $stockQuery->where(function ($q) use ($excludeSaleId) {
                        $q->whereNull('sale_id')
                            ->orWhere('sale_id', '<>', $excludeSaleId);
                    });
                }

                $availableByProduct[$productId] = round((float) $stockQuery->sum('qty'), 3);
            }

            if ($qty > $availableByProduct[$productId]) {
                throw ValidationException::withMessages([
                    'qty.' . $i => 'Insufficient stock for ' . $product->product_name . '. Available: ' . number_format($availableByProduct[$productId], 3) . ', requested: ' . number_format($qty, 3) . '.',
                ]);
            }

            $availableByProduct[$productId] = round($availableByProduct[$productId] - $qty, 3);

            $rate = $unitName === 'sales_uom'
                ? round((float) ($product->sales_price_su ?? 0), 2)
                : round((float) ($product->sales_price_bu ?? $product->sale_price ?? 0), 2);
            $amount = round($qty * $rate, 2);

            $cgstPercent = round((float) ($product->cgst_percent ?? 0), 2);
            $sgstPercent = round((float) ($product->sgst_percent ?? 0), 2);
            $igstPercent = round((float) ($product->igst_percent ?? 0), 2);

            $cgstAmount = round($amount * $cgstPercent / 100, 2);
            $sgstAmount = round($amount * $sgstPercent / 100, 2);
            $igstAmount = round($amount * $igstPercent / 100, 2);
            $gstAmount = round($cgstAmount + $sgstAmount + $igstAmount, 2);
            $netAmount = round($amount + $gstAmount, 2);

            $invoiceAmount += $netAmount;

            $details[] = [
                'product_id' => $productId,
                'item_code' => $product->product_code,
                'product_name' => $product->product_name,
                'uom' => $resolvedUom,
                'unit_name' => $unitName,
                'qty' => $qty,
                'rate' => $rate,
                'amount' => $amount,
                'cgst_percent' => $cgstPercent,
                'cgst_amount' => $cgstAmount,
                'sgst_percent' => $sgstPercent,
                'sgst_amount' => $sgstAmount,
                'igst_percent' => $igstPercent,
                'igst_amount' => $igstAmount,
                'gst_amount' => $gstAmount,
                'net_amount' => $netAmount,
                'total' => $netAmount,
            ];
        }

        $discountAmount = round((float) ($validated['discount_amount'] ?? 0), 2);
        $discountAmount = min($discountAmount, round($invoiceAmount, 2));
        $totalAmount = max(0, round($invoiceAmount - $discountAmount, 2));

        return [
            'details' => $details,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
        ];
    }

    private function syncStockForSale(SaleMaster $sale, array $details, string $saleDate): void
    {
        Stock::query()->where('sale_id', $sale->id)->delete();

        foreach ($details as $detail) {
            Stock::create([
                'purchase_id' => null,
                'sale_id' => $sale->id,
                'entry_date' => $saleDate,
                'product_id' => $detail['product_id'],
                'product_name' => $detail['product_name'],
                'uom' => $detail['uom'] ?? null,
                'qty' => -1 * (float) $detail['qty'],
                'supplier_id' => null,
                'batch_id' => 'SAL-' . $sale->invoice_no,
            ]);
        }
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
