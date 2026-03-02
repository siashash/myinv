<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SaleMaster;
use App\Support\RolePermissionAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesReceiptController extends Controller
{
    private const MODULE_NAMES = ['sales-receipt', 'receipt-sales', 'sales', 'transaction-sales'];

    public function index(Request $request): View
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'view'), 403);

        $customers = Customer::query()->orderBy('customer_name')->get(['customer_id', 'customer_name']);
        $selectedCustomerId = (string) $request->input('customer_id', '');

        $query = SaleMaster::query()
            ->where('sale_mode', 'Credit')
            ->withSum('salesReturns as return_amount', 'total_return_amount')
            ->orderBy('sale_date', 'desc')
            ->orderBy('id', 'desc');

        if ($selectedCustomerId !== '') {
            $query->where('customer_id', (int) $selectedCustomerId);
        }

        $rows = $query->get(['id', 'sale_date', 'invoice_no', 'customer_id', 'customer_name', 'total_amount'])
            ->map(function (SaleMaster $sale) {
                $returnAmount = round((float) ($sale->return_amount ?? 0), 2);
                $sale->return_amount = $returnAmount;
                $sale->receivable_amount = max(0, round((float) $sale->total_amount - $returnAmount, 2));

                return $sale;
            });

        return view('sales_receipts.index', [
            'customers' => $customers,
            'rows' => $rows,
            'selectedCustomerId' => $selectedCustomerId,
        ]);
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
