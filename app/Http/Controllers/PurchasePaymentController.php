<?php

namespace App\Http\Controllers;

use App\Models\PurchaseMaster;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchasePaymentController extends Controller
{
    public function index(Request $request): View
    {
        $suppliers = Supplier::orderBy('supplier_name')->get(['supplier_id', 'supplier_name']);
        $selectedSupplierId = (string) $request->input('supplier_id', '');

        $purchases = collect();

        if ($selectedSupplierId !== '') {
            $purchases = PurchaseMaster::query()
                ->where('supplier_id', (int) $selectedSupplierId)
                ->withSum('payments as paid_amount', 'payment_amount')
                ->orderBy('purchase_date', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $purchases->transform(function (PurchaseMaster $purchase) {
                $paidAmount = round((float) ($purchase->paid_amount ?? 0), 2);
                $invoiceAmount = round((float) $purchase->invoice_amount, 2);
                $balanceAmount = max(0, round($invoiceAmount - $paidAmount, 2));

                $purchase->paid_amount = $paidAmount;
                $purchase->balance_amount = $balanceAmount;

                return $purchase;
            });
        }

        return view('purchase_payments.index', [
            'suppliers' => $suppliers,
            'purchases' => $purchases,
            'selectedSupplierId' => $selectedSupplierId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,supplier_id'],
            'purchase_id' => ['required', 'exists:purchase_master,id'],
            'payment_amount' => ['required', 'numeric', 'gt:0'],
            'payment_mode' => ['required', Rule::in(['Cash', 'Cheque', 'UPI'])],
        ]);

        DB::transaction(function () use ($validated): void {
            $purchase = PurchaseMaster::query()
                ->lockForUpdate()
                ->findOrFail((int) $validated['purchase_id']);

            if ((int) $purchase->supplier_id !== (int) $validated['supplier_id']) {
                throw ValidationException::withMessages([
                    'supplier_id' => 'Selected invoice does not belong to selected supplier.',
                ]);
            }

            $alreadyPaid = (float) PurchasePayment::query()
                ->where('purchase_id', $purchase->id)
                ->sum('payment_amount');

            $invoiceAmount = round((float) $purchase->invoice_amount, 2);
            $balanceAmount = max(0, round($invoiceAmount - $alreadyPaid, 2));
            $paymentAmount = round((float) $validated['payment_amount'], 2);

            if ($paymentAmount > $balanceAmount) {
                throw ValidationException::withMessages([
                    'payment_amount' => 'Payment amount cannot exceed balance amount ('.number_format($balanceAmount, 2).').',
                ]);
            }

            PurchasePayment::create([
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'supplier_name' => $purchase->supplier_name,
                'supplier_inv_no' => $purchase->supplier_inv_no,
                'invoice_amount' => $invoiceAmount,
                'payment_amount' => $paymentAmount,
                'payment_mode' => $validated['payment_mode'],
                'payment_date' => now()->toDateString(),
            ]);
        });

        return redirect()
            ->route('purchase-payments.index', ['supplier_id' => $validated['supplier_id']])
            ->with('success', 'Payment saved successfully.');
    }
}
