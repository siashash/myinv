<?php

namespace App\Http\Controllers;

use App\Models\PurchasePayment;
use App\Models\PurchaseMaster;
use App\Models\PurchaseReturn;
use App\Models\SaleMaster;
use App\Models\SalesReturn;
use App\Support\RolePermissionAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BookReportController extends Controller
{
    private const MODULE_NAMES = ['reports', 'account-book', 'cash-book', 'bank-book'];

    public function account(Request $request): View
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'view'), 403);

        return view('reports.account_book', [
            'rows' => $this->buildRows('all', $request),
            'title' => 'Account book',
            'filters' => [
                'date_from' => (string) $request->input('date_from', ''),
                'date_to' => (string) $request->input('date_to', ''),
            ],
        ]);
    }

    public function cash(Request $request): View
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'view'), 403);

        return view('reports.cash_book', [
            'rows' => $this->buildRows('cash', $request),
            'title' => 'Cash book',
        ]);
    }

    public function bank(Request $request): View
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'view'), 403);

        return view('reports.bank_book', [
            'rows' => $this->buildRows('bank', $request),
            'title' => 'Bank book',
        ]);
    }

    private function buildRows(string $bookType, Request $request): Collection
    {
        $purchaseInvoicesQuery = PurchaseMaster::query()
            ->orderBy('purchase_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('date_from')) {
            $purchaseInvoicesQuery->whereDate('purchase_date', '>=', $request->string('date_from'));
        }
        if ($request->filled('date_to')) {
            $purchaseInvoicesQuery->whereDate('purchase_date', '<=', $request->string('date_to'));
        }

        $purchasePayments = $purchaseInvoicesQuery->get([
            'purchase_date',
            'supplier_name',
            'supplier_inv_no',
            'purchase_mode',
            'invoice_amount',
        ])->toBase()
            ->map(function (PurchaseMaster $row) {
                return [
                    'date' => (string) $row->purchase_date,
                    'side' => 'Debit',
                    'transaction_details' => 'Purchase - ' . (string) ($row->supplier_name ?: '-') . ' / ' . (string) ($row->supplier_inv_no ?: '-'),
                    'mode' => (string) ($row->purchase_mode ?: '-'),
                    'amount' => round((float) $row->invoice_amount, 2),
                ];
            });

        $salesQuery = SaleMaster::query()
            ->orderBy('sale_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('date_from')) {
            $salesQuery->whereDate('sale_date', '>=', $request->string('date_from'));
        }
        if ($request->filled('date_to')) {
            $salesQuery->whereDate('sale_date', '<=', $request->string('date_to'));
        }

        $salesReceipts = $salesQuery->get([
            'sale_date',
            'invoice_no',
            'customer_name',
            'sale_mode',
            'total_amount',
        ])->toBase()
            ->map(function (SaleMaster $row) {
                return [
                    'date' => (string) $row->sale_date,
                    'side' => 'Credit',
                    'transaction_details' => 'Sales Receipt - ' . (string) ($row->customer_name ?: '-') . ' / ' . (string) ($row->invoice_no ?: '-'),
                    'mode' => (string) ($row->sale_mode ?: '-'),
                    'amount' => round((float) $row->total_amount, 2),
                ];
            });

        $rows = $purchasePayments->merge($salesReceipts);

        if ($bookType === 'all') {
            $salesReturnsQuery = SalesReturn::query()
                ->orderBy('return_date', 'desc')
                ->orderBy('id', 'desc');

            if ($request->filled('date_from')) {
                $salesReturnsQuery->whereDate('return_date', '>=', $request->string('date_from'));
            }
            if ($request->filled('date_to')) {
                $salesReturnsQuery->whereDate('return_date', '<=', $request->string('date_to'));
            }

            $salesReturns = $salesReturnsQuery->get([
                'return_date',
                'return_no',
                'sale_invoice_no',
                'customer_name',
                'total_return_amount',
            ])->toBase()->map(function (SalesReturn $row) {
                return [
                    'date' => (string) $row->return_date,
                    'side' => 'Debit',
                    'transaction_details' => 'Sales Return - ' . (string) ($row->customer_name ?: '-') . ' / ' . (string) ($row->sale_invoice_no ?: '-') . ' / ' . (string) ($row->return_no ?: '-'),
                    'mode' => 'Return',
                    'amount' => round((float) $row->total_return_amount, 2),
                ];
            });

            $purchaseReturnsQuery = PurchaseReturn::query()
                ->orderBy('return_date', 'desc')
                ->orderBy('id', 'desc');

            if ($request->filled('date_from')) {
                $purchaseReturnsQuery->whereDate('return_date', '>=', $request->string('date_from'));
            }
            if ($request->filled('date_to')) {
                $purchaseReturnsQuery->whereDate('return_date', '<=', $request->string('date_to'));
            }

            $purchaseReturns = $purchaseReturnsQuery->get([
                'return_date',
                'credit_note_no',
                'supplier_inv_no',
                'supplier_name',
                'total_credit_amount',
            ])->toBase()->map(function (PurchaseReturn $row) {
                return [
                    'date' => (string) $row->return_date,
                    'side' => 'Credit',
                    'transaction_details' => 'Purchase Return - ' . (string) ($row->supplier_name ?: '-') . ' / ' . (string) ($row->supplier_inv_no ?: '-') . ' / ' . (string) ($row->credit_note_no ?: '-'),
                    'mode' => 'Return',
                    'amount' => round((float) $row->total_credit_amount, 2),
                ];
            });

            $rows = $rows->merge($salesReturns)->merge($purchaseReturns);
        }

        if ($bookType === 'cash') {
            $rows = $rows->where('mode', 'Cash');
        } elseif ($bookType === 'bank') {
            $rows = $rows->reject(function (array $row) {
                return strtoupper((string) $row['mode']) === 'CASH';
            });
        }

        return $rows
            ->sortByDesc('date')
            ->values();
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
