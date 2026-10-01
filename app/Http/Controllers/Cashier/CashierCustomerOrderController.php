<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use Illuminate\Http\Request;

class CashierCustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = CustomerOrder::query()
            ->with([
                'items.product',
                'user',
                'inventoryCheckedBy',
            ])
            ->when(
                $request->filled('q'),
                function ($query) use ($request) {
                    $search = $request->q;

                    $query->where(function ($q) use ($search) {
                        $q
                            ->where(
                                'order_number',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'customer_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'customer_contact',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->when(
                $request->filled('status'),
                function ($query) use ($request) {
                    $query->where(
                        'status',
                        $request->status
                    );
                }
            )
            ->latest('order_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view(
            'cashier.cashier-customer-orders',
            [
                'orders' => $orders,
                'selectedOrder' => null,
            ]
        );
    }

    public function show(
        CustomerOrder $customerOrder
    ) {
        $customerOrder->load([
            'items.product',
            'user',
            'inventoryCheckedBy',
        ]);

        return view(
            'cashier.cashier-customer-orders',
            [
                'orders' =>
                    collect([$customerOrder]),

                'selectedOrder' =>
                    $customerOrder,
            ]
        );
    }
}