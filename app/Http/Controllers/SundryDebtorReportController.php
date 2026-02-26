<?php

namespace App\Http\Controllers;

use App\Models\PurchaseMaster;
use App\Models\PurchasePayment;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SundryDebtorReportController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::orderBy('supplier_name')->get(['supplier_id', 'supplier_name']);
        $supplierId = (string) $request->input('supplier_id', '');
        $dateFrom = (string) $request->input('date_from', '');
        $dateTo = (string) $request->input('date_to', '');

        $entries = collect();

        if ($supplierId !== '') {
            $purchaseRows = PurchaseMaster::query()
                ->where('supplier_id', (int) $supplierId)
                ->when($dateFrom !== '', function ($query) use ($dateFrom) {
                    $query->whereDate('purchase_date', '>=', $dateFrom);
                })
                ->when($dateTo !== '', function ($query) use ($dateTo) {
                    $query->whereDate('purchase_date', '<=', $dateTo);
                })
                ->get(['id', 'purchase_date', 'supplier_inv_no', 'invoice_amount'])
                ->map(function ($row) {
                    return [
                        'date' => (string) $row->purchase_date,
                        'type' => 'Purchase',
                        'debit' => round((float) $row->invoice_amount, 2),
                        'credit' => 0.0,
                        'priority' => 1,
                        'ref_id' => (int) $row->id,
                        'invoice_no' => (string) ($row->supplier_inv_no ?? ''),
                    ];
                });

            $paymentRows = PurchasePayment::query()
                ->where('supplier_id', (int) $supplierId)
                ->when($dateFrom !== '', function ($query) use ($dateFrom) {
                    $query->whereDate('payment_date', '>=', $dateFrom);
                })
                ->when($dateTo !== '', function ($query) use ($dateTo) {
                    $query->whereDate('payment_date', '<=', $dateTo);
                })
                ->get(['id', 'payment_date', 'supplier_inv_no', 'payment_amount'])
                ->map(function ($row) {
                    return [
                        'date' => (string) $row->payment_date,
                        'type' => 'Payment',
                        'debit' => 0.0,
                        'credit' => round((float) $row->payment_amount, 2),
                        'priority' => 2,
                        'ref_id' => (int) $row->id,
                        'invoice_no' => (string) ($row->supplier_inv_no ?? ''),
                    ];
                });

            $returnRows = PurchaseReturn::query()
                ->where('supplier_id', (int) $supplierId)
                ->when($dateFrom !== '', function ($query) use ($dateFrom) {
                    $query->whereDate('return_date', '>=', $dateFrom);
                })
                ->when($dateTo !== '', function ($query) use ($dateTo) {
                    $query->whereDate('return_date', '<=', $dateTo);
                })
                ->get(['id', 'return_date', 'supplier_inv_no', 'total_credit_amount'])
                ->map(function ($row) {
                    return [
                        'date' => (string) $row->return_date,
                        'type' => 'Purchase Return',
                        'debit' => 0.0,
                        'credit' => round((float) $row->total_credit_amount, 2),
                        'priority' => 3,
                        'ref_id' => (int) $row->id,
                        'invoice_no' => (string) ($row->supplier_inv_no ?? ''),
                    ];
                });

            $entries = $purchaseRows
                ->concat($paymentRows)
                ->concat($returnRows)
                ->sortBy([
                    ['date', 'asc'],
                    ['priority', 'asc'],
                    ['ref_id', 'asc'],
                ])
                ->values();

            $runningBalance = 0.0;
            $entries = $entries->map(function (array $row) use (&$runningBalance) {
                $runningBalance = round($runningBalance + $row['debit'] - $row['credit'], 2);
                $row['balance'] = $runningBalance;

                return $row;
            });
        }

        return view('reports.sundry_debtors', [
            'suppliers' => $suppliers,
            'entries' => $entries,
            'filters' => [
                'supplier_id' => $supplierId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }
}
