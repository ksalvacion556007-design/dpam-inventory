<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class CashierDashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Customer Orders
        |--------------------------------------------------------------------------
        | Cashier needs visibility of customer orders that may require
        | payment/accounting processing.
        */

        $customerOrders = CustomerOrder::query()
            ->with(['items.product'])
            ->whereIn('status', [
                'confirmed',
                'partially_fulfilled',
                'fulfilled',
            ])
            ->latest('order_date')
            ->take(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Today's Stock-Out / Released Transactions
        |--------------------------------------------------------------------------
        | These are actual product releases, not customer-order creation.
        |
        | We use InventoryMovement because your current system records
        | actual stock movement there.
        */

        $todayStockOuts = InventoryMovement::query()
            ->where('movement_type', 'stock_out')
            ->whereDate('transaction_date', today())
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Today's Released Amount
        |--------------------------------------------------------------------------
        | The amount is based on the inventory movement records that already
        | contain unit_price and amount.
        */

        $todayReleasedAmount = InventoryMovement::query()
            ->where('movement_type', 'stock_out')
            ->whereDate('transaction_date', today())
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Recent Customer-Related Stock Out
        |--------------------------------------------------------------------------
        | Useful for the Cashier to see recent actual releases/deliveries.
        */

        $recentReleases = InventoryMovement::query()
            ->with(['product', 'user'])
            ->where('movement_type', 'stock_out')
            ->latest('transaction_date')
            ->latest('id')
            ->take(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Pending Customer Orders
        |--------------------------------------------------------------------------
        | These are orders that still need inventory checking or an
        | owner decision.
        */

        $pendingOrders = CustomerOrder::query()
            ->with(['items.product'])
            ->whereIn('status', [
                'pending_inventory_check',
                'inventory_checked',
            ])
            ->latest('order_date')
            ->take(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Counts
        |--------------------------------------------------------------------------
        */

        $pendingOrderCount = CustomerOrder::query()
            ->whereIn('status', [
                'pending_inventory_check',
                'inventory_checked',
            ])
            ->count();

        $confirmedOrderCount = CustomerOrder::query()
            ->where('status', 'confirmed')
            ->count();

        $fulfilledOrderCount = CustomerOrder::query()
            ->where('status', 'fulfilled')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        return view('cashier.cashier-dashboard', [
            'customerOrders' => $customerOrders,
            'todayStockOuts' => $todayStockOuts,
            'todayReleasedAmount' => $todayReleasedAmount,
            'recentReleases' => $recentReleases,
            'pendingOrders' => $pendingOrders,
            'pendingOrderCount' => $pendingOrderCount,
            'confirmedOrderCount' => $confirmedOrderCount,
            'fulfilledOrderCount' => $fulfilledOrderCount,
        ]);
    }
}