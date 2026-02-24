<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseDetail;
use App\Models\PurchaseMaster;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('supplier_name')->get(['supplier_id', 'supplier_name']);
        $products = Product::orderBy('product_name')->get([
            'id',
            'product_name',
            'hsn_code',
            'uom',
            'sales_uom',
            'sales_price_bu',
            'sales_price_su',
            'sale_price',
            'cgst_percent',
            'sgst_percent',
            'igst_percent',
        ]);
        $purchases = PurchaseMaster::with('supplier')
            ->withSum('details as total_qty', 'qty')
            ->orderBy('id', 'desc')
            ->get();

        return view('purchases.index', compact('suppliers', 'products', 'purchases'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePurchase($request);

        DB::transaction(function () use ($validated): void {
            $supplier = Supplier::findOrFail((int) $validated['supplier_id']);
            $detailsPayload = $this->buildDetailsPayload($validated);

            $purchase = PurchaseMaster::create([
                'entry_date' => $validated['entry_date'],
                'supplier_id' => $validated['supplier_id'],
                'supplier_name' => $supplier->supplier_name,
                'supplier_inv_no' => $validated['supplier_inv_no'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'tot_taxable_amount' => $detailsPayload['tot_taxable_amount'],
                'tot_gst_amount' => $detailsPayload['tot_gst_amount'],
                'invoice_amount' => $detailsPayload['invoice_amount'],
                'purchase_mode' => $validated['purchase_mode'],
            ]);

            foreach ($detailsPayload['details'] as $detail) {
                $purchase->details()->create($detail);
            }
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase created successfully.');
    }

    public function edit(PurchaseMaster $purchase)
    {
        $purchase->load('details');
        $suppliers = Supplier::orderBy('supplier_name')->get(['supplier_id', 'supplier_name']);
        $products = Product::orderBy('product_name')->get([
            'id',
            'product_name',
            'hsn_code',
            'uom',
            'sales_uom',
            'sales_price_bu',
            'sales_price_su',
            'sale_price',
            'cgst_percent',
            'sgst_percent',
            'igst_percent',
        ]);

        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseMaster $purchase)
    {
        $validated = $this->validatePurchase($request);

        DB::transaction(function () use ($validated, $purchase): void {
            $supplier = Supplier::findOrFail((int) $validated['supplier_id']);
            $detailsPayload = $this->buildDetailsPayload($validated);

            $purchase->update([
                'entry_date' => $validated['entry_date'],
                'supplier_id' => $validated['supplier_id'],
                'supplier_name' => $supplier->supplier_name,
                'supplier_inv_no' => $validated['supplier_inv_no'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'tot_taxable_amount' => $detailsPayload['tot_taxable_amount'],
                'tot_gst_amount' => $detailsPayload['tot_gst_amount'],
                'invoice_amount' => $detailsPayload['invoice_amount'],
                'purchase_mode' => $validated['purchase_mode'],
            ]);

            $purchase->details()->delete();
            foreach ($detailsPayload['details'] as $detail) {
                $purchase->details()->create($detail);
            }
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully.');
    }

    public function destroy(PurchaseMaster $purchase)
    {
        $purchase->delete();

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
    }

    public function details(Request $request)
    {
        $suppliers = Supplier::orderBy('supplier_name')->get(['supplier_id', 'supplier_name']);

        $query = PurchaseDetail::query()
            ->join('purchase_master', 'purchase_master.id', '=', 'purchase_details.pur_id')
            ->select([
                'purchase_details.*',
                'purchase_master.purchase_date',
                'purchase_master.supplier_name',
                'purchase_master.supplier_id',
                'purchase_master.supplier_inv_no',
                'purchase_master.purchase_mode',
            ])
            ->orderBy('purchase_master.purchase_date', 'desc')
            ->orderBy('purchase_details.id', 'desc');

        if ($request->filled('purchase_date')) {
            $query->whereDate('purchase_master.purchase_date', $request->string('purchase_date'));
        }

        if ($request->filled('supplier_id')) {
            $query->where('purchase_master.supplier_id', (int) $request->input('supplier_id'));
        }

        $rows = $query->get();

        return view('purchases.details', [
            'suppliers' => $suppliers,
            'rows' => $rows,
            'filters' => [
                'purchase_date' => (string) $request->input('purchase_date', ''),
                'supplier_id' => (string) $request->input('supplier_id', ''),
            ],
        ]);
    }

    private function validatePurchase(Request $request): array
    {
        return $request->validate([
            'entry_date' => ['required', 'date'],
            'supplier_id' => ['required', 'exists:suppliers,supplier_id'],
            'supplier_inv_no' => ['nullable', 'string', 'max:100'],
            'purchase_date' => ['required', 'date'],
            'purchase_mode' => ['required', Rule::in(['Cash', 'Credit', 'UPI'])],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'exists:products,id'],
            'unit_name' => ['required', 'array', 'min:1'],
            'unit_name.*' => ['required', Rule::in(['uom', 'sales_uom'])],
            'qty' => ['required', 'array', 'min:1'],
            'qty.*' => ['required', 'numeric', 'gt:0'],
        ]);
    }

    private function buildDetailsPayload(array $validated): array
    {
        $productIds = $validated['product_id'];
        $unitNameList = $validated['unit_name'];
        $qtyList = $validated['qty'];
        $count = count($productIds);
        if (
            $count !== count($qtyList)
            || $count !== count($unitNameList)
        ) {
            throw ValidationException::withMessages([
                'product_id' => 'Purchase details rows are invalid.',
            ]);
        }

        $products = Product::whereIn('id', $productIds)
            ->get([
                'id',
                'product_name',
                'hsn_code',
                'uom',
                'sales_uom',
                'sales_price_bu',
                'sales_price_su',
                'cgst_percent',
                'sgst_percent',
                'igst_percent',
            ])
            ->keyBy('id');

        $details = [];
        $totTaxableAmount = 0;
        $totGstAmount = 0;

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
            $salesPriceBu = round((float) ($product->sales_price_bu ?? 0), 2);
            $salesPriceSu = round((float) ($product->sales_price_su ?? 0), 2);
            $salePrice = $unitName === 'sales_uom' ? $salesPriceSu : $salesPriceBu;
            $amount = round($qty * $salePrice, 2);

            $cgstPercent = round((float) ($product->cgst_percent ?? 0), 2);
            $sgstPercent = round((float) ($product->sgst_percent ?? 0), 2);
            $igstPercent = round((float) ($product->igst_percent ?? 0), 2);

            $cgstAmount = round($amount * $cgstPercent / 100, 2);
            $sgstAmount = round($amount * $sgstPercent / 100, 2);
            $igstAmount = round($amount * $igstPercent / 100, 2);

            $gstAmount = round($cgstAmount + $sgstAmount + $igstAmount, 2);
            $netAmount = round($amount + $gstAmount, 2);

            $totTaxableAmount += $amount;
            $totGstAmount += $gstAmount;

            $details[] = [
                'product_id' => $productId,
                'product_name' => $product->product_name,
                'hsn_code' => $product->hsn_code,
                'sales_unit' => $unitName === 'sales_uom' ? $product->sales_uom : $product->uom,
                'qty' => $qty,
                'sale_price' => $salePrice,
                'amount' => $amount,
                'cgst_percent' => $cgstPercent,
                'cgst_amount' => $cgstAmount,
                'sgst_percent' => $sgstPercent,
                'sgst_amount' => $sgstAmount,
                'igst_percent' => $igstPercent,
                'igst_amount' => $igstAmount,
                'gst_amount' => $gstAmount,
                'net_amount' => $netAmount,
            ];
        }

        $totTaxableAmount = round($totTaxableAmount, 2);
        $totGstAmount = round($totGstAmount, 2);

        return [
            'details' => $details,
            'tot_taxable_amount' => $totTaxableAmount,
            'tot_gst_amount' => $totGstAmount,
            'invoice_amount' => round($totTaxableAmount + $totGstAmount, 2),
        ];
    }
}
