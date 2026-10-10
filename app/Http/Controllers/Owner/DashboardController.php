<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Active Products
        |--------------------------------------------------------------------------
        */

        $products = Product::with('inventory')
            ->where('status', 'active')
            ->orderBy('product_name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Stock Status
        |--------------------------------------------------------------------------
        */

        $lowStockItems = collect();
        $outOfStockItems = collect();

        $totalUnitsInStock = 0;


        foreach ($products as $product) {

            $currentStock = (int) (
                $product->inventory?->current_stock ?? 0
            );

            $reorderLevel = (int) (
                $product->reorder_level ?? 0
            );


            /*
            |--------------------------------------------------------------------------
            | Make current stock directly available to Blade
            |--------------------------------------------------------------------------
            */

            $product->current_stock = $currentStock;


            $totalUnitsInStock += $currentStock;


            /*
            |--------------------------------------------------------------------------
            | Determine Stock Status
            |--------------------------------------------------------------------------
            */

            if ($currentStock <= 0) {

                $outOfStockItems->push($product);

            } elseif ($currentStock <= $reorderLevel) {

                $lowStockItems->push($product);

            }
        }


        /*
        |--------------------------------------------------------------------------
        | Stock Status Counts
        |--------------------------------------------------------------------------
        */

        $outOfStockCount = $outOfStockItems->count();

        $lowStockCount = $lowStockItems->count();

        $inStockCount = max(
            0,
            $products->count()
                - $lowStockCount
                - $outOfStockCount
        );


        /*
        |--------------------------------------------------------------------------
        | Purchase Order Status Counts
        |--------------------------------------------------------------------------
        */

        $poStatusCounts = PurchaseOrder::selectRaw(
            'status, COUNT(*) as total'
        )
            ->groupBy('status')
            ->pluck('total', 'status');


        /*
        |--------------------------------------------------------------------------
        | Open Purchase Orders
        |--------------------------------------------------------------------------
        |
        | These are purchase orders that still require
        | processing, follow-up, or completion.
        |
        */

        $openPurchaseOrderStatuses = [
            'draft',
            'pending',
            'confirmed',
            'approved',
            'purchasing',
            'partial',
        ];


        $openPurchaseOrderCount = PurchaseOrder::whereIn(
            'status',
            $openPurchaseOrderStatuses
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Sort Low Stock Items
        |--------------------------------------------------------------------------
        |
        | Products with the lowest stock appear first.
        |
        */

        $lowStockItems = $lowStockItems
            ->sortBy('current_stock')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Sort Out of Stock Items
        |--------------------------------------------------------------------------
        */

        $outOfStockItems = $outOfStockItems
            ->sortBy('product_name')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Open Purchase Orders
        |--------------------------------------------------------------------------
        */

        $openPurchaseOrders = PurchaseOrder::with('supplier')
            ->whereIn(
                'status',
                $openPurchaseOrderStatuses
            )
            ->latest()
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Dashboard View
        |--------------------------------------------------------------------------
        */

        return view('owner.dashboard', [
            'totalProducts' => $products->count(),
            'totalUnitsInStock' => $totalUnitsInStock,

            'inStockCount' => $inStockCount,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,

            // IMPORTANT:
            // Send the complete active product collection to the dashboard.
            'products' => $products,

            'lowStockItems' => $lowStockItems,
            'outOfStockItems' => $outOfStockItems,

            'openPurchaseOrderCount' => $openPurchaseOrderCount,
            'openPurchaseOrders' => $openPurchaseOrders,

            'poStatusCounts' => $poStatusCounts,
        ]);
    }
}