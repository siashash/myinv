<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SaleDetail;
use App\Support\RolePermissionAccess;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    private const MODULE_NAMES = ['sales-report', 'reports', 'sales', 'transaction-sales'];

    public function index(Request $request)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'view'), 403);

        $customers = Customer::orderBy('customer_name')->get(['customer_id', 'customer_name']);

        $query = SaleDetail::query()
            ->join('sales_master', 'sales_master.id', '=', 'sales_details.sale_id')
            ->select([
                'sales_master.sale_date',
                'sales_master.invoice_no',
                'sales_master.customer_id',
                'sales_master.customer_name',
                'sales_master.total_amount as invoice_amount',
                'sales_details.product_name',
                'sales_details.uom',
                'sales_details.qty',
                'sales_details.rate',
                'sales_details.amount',
                'sales_details.cgst_percent',
                'sales_details.sgst_percent',
                'sales_details.igst_percent',
                'sales_details.gst_amount',
            ])
            ->orderBy('sales_master.sale_date', 'desc')
            ->orderBy('sales_details.id', 'desc');

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->string('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->string('date_to'));
        }
        if ($request->filled('customer_id')) {
            $query->where('sales_master.customer_id', (int) $request->input('customer_id'));
        }

        $rows = $query->get();

        return view('reports.sales', [
            'customers' => $customers,
            'rows' => $rows,
            'filters' => [
                'date_from' => (string) $request->input('date_from', ''),
                'date_to' => (string) $request->input('date_to', ''),
                'customer_id' => (string) $request->input('customer_id', ''),
            ],
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
