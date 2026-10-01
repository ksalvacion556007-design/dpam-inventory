<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;

class SecretaryDashboardController extends Controller
{
    /**
     * Display the Secretary Dashboard.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | ACTIVE PRODUCTS
        |--------------------------------------------------------------------------
        */

        $totalProducts = Product::where('status', 'active')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | CURRENT INVENTORY
        |--------------------------------------------------------------------------
        */

        $totalInventoryQuantity = Inventory::whereHas('product', function ($query) {
            $query->where('status', 'active');
        })
            ->sum('current_stock');


        /*
        |--------------------------------------------------------------------------
        | LOW STOCK COUNT
        |--------------------------------------------------------------------------
        |
        | Low stock means:
        | - Product is active
        | - Current stock is greater than 0
        | - Current stock is at or below reorder level
        |
        */

        $lowStockCount = Inventory::whereHas('product', function ($query) {
            $query->where('status', 'active');
        })
            ->join(
                'products',
                'inventories.product_id',
                '=',
                'products.id'
            )
            ->where('inventories.current_stock', '>', 0)
            ->whereColumn(
                'inventories.current_stock',
                '<=',
                'products.reorder_level'
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | OUT OF STOCK COUNT
        |--------------------------------------------------------------------------
        */

        $outOfStockCount = Inventory::whereHas('product', function ($query) {
            $query->where('status', 'active');
        })
            ->where('current_stock', '<=', 0)
            ->count();


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER ORDERS WAITING FOR INVENTORY CHECK
        |--------------------------------------------------------------------------
        |
        | Customer Order creation does NOT deduct inventory.
        | These orders are waiting for the Secretary/Owner to check
        | product availability.
        |
        */

        $pendingInventoryChecks = CustomerOrder::where(
            'status',
            'pending_inventory_check'
        )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | RECENT INVENTORY MOVEMENTS
        |--------------------------------------------------------------------------
        |
        | Uses transaction_date because this represents the actual
        | inventory transaction date.
        |
        */

        $recentMovements = InventoryMovement::with([
            'product',
            'user',
        ])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->take(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | LOW STOCK PRODUCTS
        |--------------------------------------------------------------------------
        |
        | This list is used by the dashboard to show products that
        | have reached or fallen below their reorder level.
        |
        | Out-of-stock products are also included here so the dashboard
        | can identify them separately.
        |
        */

        $lowStockProducts = Inventory::with('product')
            ->whereHas('product', function ($query) {
                $query->where('status', 'active');
            })
            ->join(
                'products',
                'inventories.product_id',
                '=',
                'products.id'
            )
            ->whereColumn(
                'inventories.current_stock',
                '<=',
                'products.reorder_level'
            )
            ->select(
                'inventories.*',
                'products.product_name',
                'products.reorder_level'
            )
            ->orderBy('inventories.current_stock')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | RETURN SECRETARY DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view(
            'secretary.secretary-dashboard',
            compact(
                'totalProducts',
                'totalInventoryQuantity',
                'lowStockCount',
                'outOfStockCount',
                'pendingInventoryChecks',
                'recentMovements',
                'lowStockProducts'
            )
        );
    }
}