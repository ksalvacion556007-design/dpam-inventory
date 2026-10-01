<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class SecretaryReportController extends Controller
{
    public function index(Request $request)
    {
        $report = $request->input('report', 'inventory-summary');

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $productId = $request->input('product_id');
        $movementType = $request->input('movement_type');
        $inventoryCheckStatus = $request->input('inventory_check_status');

        /*
        |--------------------------------------------------------------------------
        | Products for filter
        |--------------------------------------------------------------------------
        */

        $products = Product::orderBy('product_name')->get();

        /*
        |--------------------------------------------------------------------------
        | Inventory Summary
        |--------------------------------------------------------------------------
        */

        $inventorySummary = collect();

        if ($report === 'inventory-summary') {
            $inventorySummary = Product::with([
                'category',
                'inventory',
            ])
                ->where('status', 'active')
                ->orderBy('product_name')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Stock Movement
        |--------------------------------------------------------------------------
        */

        $stockMovements = collect();

        if ($report === 'stock-movement') {
            $stockMovements = InventoryMovement::with([
                'product.category',
                'user',
            ])
                ->when($dateFrom, function ($query) use ($dateFrom) {
                    $query->whereDate('transaction_date', '>=', $dateFrom);
                })
                ->when($dateTo, function ($query) use ($dateTo) {
                    $query->whereDate('transaction_date', '<=', $dateTo);
                })
                ->when($productId, function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->when($movementType, function ($query) use ($movementType) {
                    $query->where('movement_type', $movementType);
                })
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Stock In Report
        |--------------------------------------------------------------------------
        */

        $stockIns = collect();

        if ($report === 'stock-in') {
            $stockIns = InventoryMovement::with([
                'product.category',
                'user',
            ])
                ->where('movement_type', 'stock_in')
                ->when($dateFrom, function ($query) use ($dateFrom) {
                    $query->whereDate('transaction_date', '>=', $dateFrom);
                })
                ->when($dateTo, function ($query) use ($dateTo) {
                    $query->whereDate('transaction_date', '<=', $dateTo);
                })
                ->when($productId, function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Stock Out Report
        |--------------------------------------------------------------------------
        */

        $stockOuts = collect();

        if ($report === 'stock-out') {
            $stockOuts = InventoryMovement::with([
                'product.category',
                'user',
            ])
                ->where('movement_type', 'stock_out')
                ->when($dateFrom, function ($query) use ($dateFrom) {
                    $query->whereDate('transaction_date', '>=', $dateFrom);
                })
                ->when($dateTo, function ($query) use ($dateTo) {
                    $query->whereDate('transaction_date', '<=', $dateTo);
                })
                ->when($productId, function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Low Stock / Reorder Report
        |--------------------------------------------------------------------------
        */

        $lowStockProducts = collect();

        if ($report === 'low-stock') {
            $lowStockProducts = Product::with([
                'category',
                'inventory',
            ])
                ->where('status', 'active')
                ->whereHas('inventory', function ($query) {
                    $query->whereColumn(
                        'current_stock',
                        '<=',
                        'products.reorder_level'
                    );
                })
                ->orderBy('product_name')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Customer Order Inventory Report
        |--------------------------------------------------------------------------
        */

        $customerOrders = collect();

        if ($report === 'customer-orders') {
            $customerOrders = CustomerOrder::with([
                'items.product',
                'inventoryCheckedBy',
                'user',
            ])
                ->when($dateFrom, function ($query) use ($dateFrom) {
                    $query->whereDate('order_date', '>=', $dateFrom);
                })
                ->when($dateTo, function ($query) use ($dateTo) {
                    $query->whereDate('order_date', '<=', $dateTo);
                })
                ->when($inventoryCheckStatus, function ($query) use ($inventoryCheckStatus) {
                    $query->where(
                        'inventory_check_status',
                        $inventoryCheckStatus
                    );
                })
                ->orderByDesc('order_date')
                ->orderByDesc('id')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Inventory Adjustment Report
        |--------------------------------------------------------------------------
        */

        $adjustments = collect();

        if ($report === 'adjustments') {
            $adjustments = InventoryMovement::with([
                'product.category',
                'user',
            ])
                ->where('movement_type', 'adjustment')
                ->when($dateFrom, function ($query) use ($dateFrom) {
                    $query->whereDate('transaction_date', '>=', $dateFrom);
                })
                ->when($dateTo, function ($query) use ($dateTo) {
                    $query->whereDate('transaction_date', '<=', $dateTo);
                })
                ->when($productId, function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->get();
        }

        return view(
            'secretary.secretary-reports',
            compact(
                'report',
                'dateFrom',
                'dateTo',
                'productId',
                'movementType',
                'inventoryCheckStatus',
                'products',
                'inventorySummary',
                'stockMovements',
                'stockIns',
                'stockOuts',
                'lowStockProducts',
                'customerOrders',
                'adjustments'
            )
        );
    }
}