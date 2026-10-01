<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecretaryCustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $status = $request->input('status');

        $orders = CustomerOrder::with([
            'items.product.category',
            'items.product.inventory',
            'user',
            'inventoryCheckedBy',
        ])
            ->when(
                $search,
                function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where(
                                'order_number',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'customer_name',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'customer_contact',
                                'like',
                                '%' . $search . '%'
                            );
                    });
                }
            )
            ->when(
                $status,
                function ($query, $status) {
                    $query->where(
                        'status',
                        $status
                    );
                }
            )
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->get();

        $pendingCount = CustomerOrder::where(
            'status',
            'pending_inventory_check'
        )->count();

        $availableCount = CustomerOrder::where(
            'inventory_check_status',
            'available'
        )
            ->whereNotIn('status', ['cancelled'])
            ->count();

        $insufficientCount = CustomerOrder::where(
            'inventory_check_status',
            'insufficient'
        )
            ->whereNotIn('status', ['cancelled'])
            ->count();

        $totalOrders = CustomerOrder::count();

        return view(
            'secretary.secretary-customer-orders',
            compact(
                'orders',
                'search',
                'status',
                'pendingCount',
                'availableCount',
                'insufficientCount',
                'totalOrders'
            )
        );
    }

    public function checkInventory(
        Request $request,
        CustomerOrder $customerOrder
    ) {
        $validated = $request->validate([
            'inventory_check_notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        /*
         * Do not re-check completed/cancelled orders.
         */
        if (
            in_array(
                $customerOrder->status,
                ['fulfilled', 'cancelled'],
                true
            )
        ) {
            return back()->withErrors([
                'inventory_check' =>
                    'This customer order can no longer be checked.',
            ]);
        }

        $customerOrder->load([
            'items.product.inventory',
        ]);

        $insufficientItems = [];

        foreach ($customerOrder->items as $item) {
            $product = $item->product;

            if (!$product) {
                $insufficientItems[] = [
                    'product' => 'Product unavailable',
                    'requested' => (int) $item->quantity,
                    'physical_stock' => 0,
                    'committed' => 0,
                    'available_to_promise' => 0,
                    'shortage' => (int) $item->quantity,
                ];

                continue;
            }

            $inventory = $product->inventory;

            $physicalStock = $inventory
                ? (int) $inventory->current_stock
                : 0;

            /*
             * Sum reservations belonging to OTHER active orders.
             */
            $committedToOtherOrders =
                CustomerOrderItem::where(
                    'product_id',
                    $product->id
                )
                    ->where(
                        'customer_order_id',
                        '!=',
                        $customerOrder->id
                    )
                    ->whereHas(
                        'customerOrder',
                        function ($query) {
                            $query->whereNotIn('status', [
                                'cancelled',
                                'fulfilled',
                            ]);
                        }
                    )
                    ->sum('reserved_quantity');

            /*
             * The current order's own reservation is included back
             * because we are checking what this order can actually
             * receive.
             */
            $ownReservation =
                (int) $item->reserved_quantity;

            $availableToPromise =
                $physicalStock -
                (int) $committedToOtherOrders +
                $ownReservation;

            $availableToPromise = max(
                0,
                $availableToPromise
            );

            $requestedQuantity =
                (int) $item->quantity;

            if (
                $availableToPromise <
                $requestedQuantity
            ) {
                $insufficientItems[] = [
                    'product' =>
                        $product->product_name,

                    'requested' =>
                        $requestedQuantity,

                    'physical_stock' =>
                        $physicalStock,

                    'committed' =>
                        (int) $committedToOtherOrders,

                    'available_to_promise' =>
                        $availableToPromise,

                    'shortage' =>
                        $requestedQuantity -
                        $availableToPromise,
                ];
            }
        }

        if (count($insufficientItems) > 0) {
            $inventoryCheckStatus =
                'insufficient';

            $defaultNote =
                'Inventory is insufficient after considering physical stock and quantities already committed to other customer orders.';
        } else {
            $inventoryCheckStatus =
                'available';

            $defaultNote =
                'All requested products are currently available after considering physical stock and quantities already committed to other customer orders.';
        }

        $note =
            $validated['inventory_check_notes']
            ?? $defaultNote;

        $customerOrder->update([
            'inventory_check_status' =>
                $inventoryCheckStatus,

            'inventory_checked_by' =>
                Auth::id(),

            'inventory_checked_at' =>
                now(),

            'inventory_check_notes' =>
                $note,

            'status' =>
                'inventory_checked',
        ]);

        if (
            $inventoryCheckStatus ===
            'available'
        ) {
            return redirect()
                ->route(
                    'secretary.customer-orders'
                )
                ->with(
                    'success',
                    'Inventory check completed. The order has sufficient available-to-promise stock and has been reported to the Owner.'
                );
        }

        return redirect()
            ->route(
                'secretary.customer-orders'
            )
            ->with(
                'warning',
                'Inventory check completed. The order has insufficient available-to-promise stock and has been reported to the Owner.'
            );
    }
}