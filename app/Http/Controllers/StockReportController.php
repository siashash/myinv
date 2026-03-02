<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Support\RolePermissionAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockReportController extends Controller
{
    private const MODULE_NAMES = ['stock-report', 'stock', 'reports'];

    public function index(Request $request)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'view'), 403);

        $products = Product::orderBy('product_name')->get(['id', 'product_name', 'product_code']);
        $selectedProductId = (string) $request->input('product_id', '');

        $stockAgg = Stock::query()
            ->select([
                'product_id',
                DB::raw('SUM(CASE WHEN purchase_id IS NOT NULL AND qty > 0 THEN qty ELSE 0 END) as purchase_qty'),
                DB::raw('SUM(CASE WHEN purchase_id IS NOT NULL AND qty < 0 THEN ABS(qty) ELSE 0 END) as purchase_return_qty'),
                DB::raw('SUM(CASE WHEN sale_id IS NOT NULL AND qty < 0 THEN ABS(qty) ELSE 0 END) as sales_qty'),
                DB::raw('SUM(CASE WHEN sale_id IS NOT NULL AND qty > 0 THEN qty ELSE 0 END) as sales_return_qty'),
            ])
            ->whereNotNull('product_id');

        if ($request->filled('entry_date')) {
            $stockAgg->whereDate('entry_date', '<=', $request->string('entry_date'));
        }

        if ($selectedProductId !== '') {
            $stockAgg->where('product_id', (int) $selectedProductId);
        }

        $stockAgg->groupBy('product_id');

        $rows = Product::query()
            ->leftJoinSub($stockAgg, 'stock_agg', function ($join) {
                $join->on('products.id', '=', 'stock_agg.product_id');
            })
            ->select([
                'products.id',
                'products.product_code',
                'products.product_name',
                'products.uom',
                DB::raw('COALESCE(products.opening_stock, 0) as opening_stock'),
                DB::raw('COALESCE(stock_agg.purchase_qty, 0) as purchase_qty'),
                DB::raw('COALESCE(stock_agg.sales_qty, 0) as sales_qty'),
                DB::raw('COALESCE(stock_agg.purchase_return_qty, 0) as purchase_return_qty'),
                DB::raw('COALESCE(stock_agg.sales_return_qty, 0) as sales_return_qty'),
                DB::raw('(COALESCE(stock_agg.purchase_qty, 0) + COALESCE(stock_agg.sales_return_qty, 0) - COALESCE(stock_agg.purchase_return_qty, 0) - COALESCE(stock_agg.sales_qty, 0)) as movement_qty'),
                DB::raw('(COALESCE(products.opening_stock, 0) + COALESCE(stock_agg.purchase_qty, 0) + COALESCE(stock_agg.sales_return_qty, 0) - COALESCE(stock_agg.purchase_return_qty, 0) - COALESCE(stock_agg.sales_qty, 0)) as current_stock'),
            ])
            ->orderBy('products.product_name')
            ->get();

        $productMovements = collect();
        $selectedProduct = null;
        if ($selectedProductId !== '') {
            $selectedProduct = Product::query()->find((int) $selectedProductId);

            $movementQuery = Stock::query()
                ->where('product_id', (int) $selectedProductId)
                ->orderBy('entry_date')
                ->orderBy('id');

            if ($request->filled('entry_date')) {
                $movementQuery->whereDate('entry_date', '<=', $request->string('entry_date'));
            }

            $openingStock = round((float) ($selectedProduct->opening_stock ?? 0), 3);
            $runningStock = $openingStock;

            $productMovements = $movementQuery->get()->map(function (Stock $row) use (&$runningStock) {
                $qty = round((float) $row->qty, 3);
                $purchaseQty = $qty > 0 ? $qty : 0.0;
                $purchaseReturnQty = $qty < 0 ? abs($qty) : 0.0;
                $runningStock = round($runningStock + $qty, 3);

                return [
                    'entry_date' => (string) $row->entry_date,
                    'batch_id' => (string) $row->batch_id,
                    'purchase_qty' => $purchaseQty,
                    'purchase_return_qty' => $purchaseReturnQty,
                    'net_qty' => $qty,
                    'balance_stock' => $runningStock,
                ];
            });
        }

        return view('reports.stock', [
            'rows' => $rows,
            'products' => $products,
            'selectedProduct' => $selectedProduct,
            'productMovements' => $productMovements,
            'filters' => [
                'entry_date' => (string) $request->input('entry_date', ''),
                'product_id' => $selectedProductId,
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
