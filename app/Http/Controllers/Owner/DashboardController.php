<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\PurchaseOrder;

class DashboardController extends Controller
{
    public function index()
    {
        $products = Product::with('inventory')
            ->where('status', 'active')
            ->get();

        $lowStockItems   = collect();
        $outOfStockItems = collect();
        $totalInventory  = 0;

        foreach ($products as $product) {
            $stock   = (int) ($product->inventory->current_stock ?? 0);
            $reorder = (int) $product->reorder_level;

            $product->current_stock = $stock;
            $totalInventory += $stock;

            if ($stock <= 0) {
                $outOfStockItems->push($product);
            } elseif ($stock <= $reorder) {
                $lowStockItems->push($product);
            }
        }

        // stock_out is stored as a negative number, so use abs()
        $totalStockIn  = (int) InventoryMovement::where('movement_type', 'stock_in')->sum('quantity');
        $totalStockOut = abs((int) InventoryMovement::where('movement_type', 'stock_out')->sum('quantity'));

        $poStatusCounts = PurchaseOrder::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('owner.dashboard', [
            'totalProducts'        => $products->count(),
            'totalInventory'       => $totalInventory,
            'lowStockCount'        => $lowStockItems->count(),
            'outOfStockCount'      => $outOfStockItems->count(),
            'lowStockItems'        => $lowStockItems->take(5),
            'outOfStockItems'      => $outOfStockItems->take(5),
            'totalStockIn'         => $totalStockIn,
            'totalStockOut'        => $totalStockOut,
            'recentMovements'      => InventoryMovement::with('product')->latest()->take(5)->get(),
            'recentPurchaseOrders' => PurchaseOrder::with('supplier')->latest()->take(5)->get(),
            'poStatusCounts'       => $poStatusCounts,
        ]);
    }
}