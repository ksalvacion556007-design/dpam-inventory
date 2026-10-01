<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the Owner Reports page.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Date Filters
        |--------------------------------------------------------------------------
        |
        | Reports can be filtered by transaction date.
        |
        */

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        /*
        |--------------------------------------------------------------------------
        | Inventory Summary
        |--------------------------------------------------------------------------
        */

        $totalProducts = Product::where('status', 'active')->count();

        $totalInventoryQuantity = Inventory::whereHas('product', function ($query) {
            $query->where('status', 'active');
        })->sum('current_stock');

        $totalInventoryValue = Inventory::whereHas('product', function ($query) {
            $query->where('status', 'active');
        })
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->selectRaw('COALESCE(SUM(inventories.current_stock * products.unit_price), 0) as total')
            ->value('total');

        /*
        |--------------------------------------------------------------------------
        | Low Stock / Out of Stock
        |--------------------------------------------------------------------------
        */

        $outOfStockCount = Inventory::whereHas('product', function ($query) {
            $query->where('status', 'active');
        })
            ->where('current_stock', '<=', 0)
            ->count();

        $lowStockCount = Inventory::whereHas('product', function ($query) {
            $query->where('status', 'active');
        })
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->where('inventories.current_stock', '>', 0)
            ->whereColumn(
                'inventories.current_stock',
                '<=',
                'products.reorder_level'
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Stock In / Stock Out Summary
        |--------------------------------------------------------------------------
        */

        $stockInQuery = InventoryMovement::where('movement_type', 'stock_in');

        $stockOutQuery = InventoryMovement::where('movement_type', 'stock_out');

        if ($dateFrom) {
            $stockInQuery->whereDate('transaction_date', '>=', $dateFrom);
            $stockOutQuery->whereDate('transaction_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $stockInQuery->whereDate('transaction_date', '<=', $dateTo);
            $stockOutQuery->whereDate('transaction_date', '<=', $dateTo);
        }

        $totalStockInQuantity = (clone $stockInQuery)->sum('quantity');

        $totalStockOutQuantity = abs(
            (clone $stockOutQuery)->sum('quantity')
        );

        $totalStockInAmount = (clone $stockInQuery)
            ->sum('amount');

        $totalStockOutAmount = (clone $stockOutQuery)
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Inventory Report
        |--------------------------------------------------------------------------
        |
        | Current inventory list.
        |
        */

        $inventoryQuery = Inventory::with('product.category')
            ->whereHas('product', function ($query) {
                $query->where('status', 'active');
            });

        $inventory = $inventoryQuery
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->select(
                'inventories.*',
                'products.product_name',
                'products.category_id',
                'products.api',
                'products.base_oil',
                'products.package_size',
                'products.unit',
                'products.unit_price',
                'products.reorder_level'
            )
            ->orderBy('products.product_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Stock In Report
        |--------------------------------------------------------------------------
        */

        $stockInReportQuery = InventoryMovement::with([
            'product.category',
            'user',
        ])
            ->where('movement_type', 'stock_in')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        if ($dateFrom) {
            $stockInReportQuery->whereDate(
                'transaction_date',
                '>=',
                $dateFrom
            );
        }

        if ($dateTo) {
            $stockInReportQuery->whereDate(
                'transaction_date',
                '<=',
                $dateTo
            );
        }

        $stockIns = $stockInReportQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Stock Out Report
        |--------------------------------------------------------------------------
        */

        $stockOutReportQuery = InventoryMovement::with([
            'product.category',
            'user',
        ])
            ->where('movement_type', 'stock_out')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        if ($dateFrom) {
            $stockOutReportQuery->whereDate(
                'transaction_date',
                '>=',
                $dateFrom
            );
        }

        if ($dateTo) {
            $stockOutReportQuery->whereDate(
                'transaction_date',
                '<=',
                $dateTo
            );
        }

        $stockOuts = $stockOutReportQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Low / Out of Stock Report
        |--------------------------------------------------------------------------
        */

        $stockStatusReport = Inventory::with('product.category')
            ->whereHas('product', function ($query) {
                $query->where('status', 'active');
            })
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->select(
                'inventories.*',
                'products.product_name',
                'products.category_id',
                'products.api',
                'products.base_oil',
                'products.package_size',
                'products.unit',
                'products.unit_price',
                'products.reorder_level'
            )
            ->where(function ($query) {
                $query->where('inventories.current_stock', '<=', 0)
                    ->orWhereColumn(
                        'inventories.current_stock',
                        '<=',
                        'products.reorder_level'
                    );
            })
            ->orderBy('inventories.current_stock')
            ->orderBy('products.product_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Purchase Order Report
        |--------------------------------------------------------------------------
        */

        $purchaseOrderQuery = PurchaseOrder::with([
            'supplier',
        ])
            ->orderByDesc('created_at');

        if ($dateFrom) {
            $purchaseOrderQuery->whereDate(
                'created_at',
                '>=',
                $dateFrom
            );
        }

        if ($dateTo) {
            $purchaseOrderQuery->whereDate(
                'created_at',
                '<=',
                $dateTo
            );
        }

        $purchaseOrders = $purchaseOrderQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Customer Order Report
        |--------------------------------------------------------------------------
        */

        $customerOrderQuery = CustomerOrder::orderByDesc('created_at');

        if ($dateFrom) {
            $customerOrderQuery->whereDate(
                'created_at',
                '>=',
                $dateFrom
            );
        }

        if ($dateTo) {
            $customerOrderQuery->whereDate(
                'created_at',
                '<=',
                $dateTo
            );
        }

        $customerOrders = $customerOrderQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Report Totals
        |--------------------------------------------------------------------------
        */

        $reportTotals = [
            'inventory_value' => $totalInventoryValue,
            'stock_in_quantity' => $totalStockInQuantity,
            'stock_in_amount' => $totalStockInAmount,
            'stock_out_quantity' => $totalStockOutQuantity,
            'stock_out_amount' => $totalStockOutAmount,
            'low_stock' => $lowStockCount,
            'out_of_stock' => $outOfStockCount,
        ];

        /*
        |--------------------------------------------------------------------------
        | Return Reports View
        |--------------------------------------------------------------------------
        */

        return view('owner.reports', compact(
            'dateFrom',
            'dateTo',
            'totalProducts',
            'totalInventoryQuantity',
            'totalInventoryValue',
            'lowStockCount',
            'outOfStockCount',
            'totalStockInQuantity',
            'totalStockOutQuantity',
            'totalStockInAmount',
            'totalStockOutAmount',
            'inventory',
            'stockIns',
            'stockOuts',
            'stockStatusReport',
            'purchaseOrders',
            'customerOrders',
            'reportTotals'
        ));
    }
}