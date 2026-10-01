<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerOrderController extends Controller
{
    public function index()
    {
        $orders = CustomerOrder::with([
            'items.product.category',
            'items.product.inventory',
            'user',
            'inventoryCheckedBy',
        ])
            ->latest('order_date')
            ->latest('id')
            ->get();

        $products = Product::with('inventory')
            ->where('status', 'active')
            ->orderBy('product_name')
            ->get();

        return view(
            'owner.customer-orders',
            compact('orders', 'products')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],

            'customer_contact' => [
                'nullable',
                'string',
                'max:255',
            ],

            'order_date' => [
                'required',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $productIds = collect($validated['items'])
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id);

        if (
            $productIds->count() !==
            $productIds->unique()->count()
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'items' =>
                        'The same product cannot be added more than once in the same customer order.',
                ]);
        }

        DB::transaction(function () use (
            $validated,
            &$order
        ) {
            $order = CustomerOrder::create([
                'order_number' => $this->generateOrderNumber(),

                'customer_name' =>
                    $validated['customer_name'],

                'customer_contact' =>
                    $validated['customer_contact'] ?? null,

                'order_date' =>
                    $validated['order_date'],

                'status' =>
                    'pending_inventory_check',

                'inventory_check_status' =>
                    'pending',

                'inventory_checked_by' =>
                    null,

                'inventory_checked_at' =>
                    null,

                'inventory_check_notes' =>
                    null,

                'owner_decision' =>
                    null,

                'notes' =>
                    $validated['notes'] ?? null,

                'user_id' =>
                    auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail(
                    $item['product_id']
                );

                if ($product->status !== 'active') {
                    abort(
                        422,
                        'Inactive or archived products cannot be added to a customer order.'
                    );
                }

                $quantity = (int) $item['quantity'];

                $unitPrice = (float) $product->unit_price;

                $subtotal = round(
                    $quantity * $unitPrice,
                    2
                );

                CustomerOrderItem::create([
                    'customer_order_id' =>
                        $order->id,

                    'product_id' =>
                        $product->id,

                    'quantity' =>
                        $quantity,

                    'reserved_quantity' =>
                        0,

                    'fulfilled_quantity' =>
                        0,

                    'unit_price' =>
                        $unitPrice,

                    'subtotal' =>
                        $subtotal,
                ]);
            }
        });

        return redirect()
            ->route('owner.customer-orders')
            ->with(
                'success',
                'Customer order created successfully and is now waiting for the Secretary inventory check.'
            );
    }

    /*
     * Owner decides what to do after inventory checking.
     *
     * IMPORTANT:
     *
     * confirmed:
     *     reserves/commits the requested quantity
     *     BUT does NOT deduct physical inventory.
     *
     * for_purchasing:
     *     does NOT reserve insufficient quantity.
     *
     * cancelled:
     *     releases any reservation belonging to the order.
     */
    public function ownerDecision(
        Request $request,
        CustomerOrder $customerOrder
    ) {
        $validated = $request->validate([
            'decision' => [
                'required',
                Rule::in([
                    'confirmed',
                    'for_purchasing',
                    'cancelled',
                ]),
            ],
        ]);

        if (
            in_array(
                $customerOrder->status,
                ['fulfilled', 'cancelled'],
                true
            )
        ) {
            return back()->withErrors([
                'decision' =>
                    'This customer order can no longer be changed.',
            ]);
        }

        $decision = $validated['decision'];

        /*
         * ==========================================================
         * CONFIRM
         * ==========================================================
         */

        if ($decision === 'confirmed') {
            $result = DB::transaction(function () use (
                $customerOrder
            ) {
                $order = CustomerOrder::with([
                    'items.product.inventory',
                ])
                    ->lockForUpdate()
                    ->findOrFail($customerOrder->id);

                /*
                 * Only an available inventory check can be confirmed.
                 */
                if (
                    $order->inventory_check_status !==
                    'available'
                ) {
                    return [
                        'success' => false,
                        'message' =>
                            'The order can only be confirmed after the inventory check reports sufficient available stock.',
                    ];
                }

                /*
                 * Lock each inventory record.
                 */
                foreach ($order->items as $item) {
                    if (!$item->product) {
                        return [
                            'success' => false,
                            'message' =>
                                'One of the products in this order is no longer available.',
                        ];
                    }

                    $inventory = $item->product->inventory;

                    if (!$inventory) {
                        return [
                            'success' => false,
                            'message' =>
                                "No inventory record exists for {$item->product->product_name}.",
                        ];
                    }

                    /*
                     * Lock the physical inventory record.
                     */
                    $lockedInventory = $inventory
                        ->newQuery()
                        ->where('id', $inventory->id)
                        ->lockForUpdate()
                        ->first();

                    /*
                     * Calculate reservations belonging to OTHER
                     * active customer orders.
                     *
                     * We exclude:
                     * - this order
                     * - cancelled orders
                     */
                    $alreadyReserved = CustomerOrderItem::where(
                        'product_id',
                        $item->product_id
                    )
                        ->where('customer_order_id', '!=', $order->id)
                        ->whereHas('customerOrder', function ($query) {
                            $query->whereNotIn('status', [
                                'cancelled',
                                'fulfilled',
                            ]);
                        })
                        ->sum('reserved_quantity');

                    $availableToPromise =
                        (int) $lockedInventory->current_stock -
                        (int) $alreadyReserved;

                    /*
                     * If this order had already reserved something,
                     * add that reservation back when calculating its
                     * own available quantity.
                     */
                    $availableToPromise +=
                        (int) $item->reserved_quantity;

                    if (
                        $availableToPromise <
                        (int) $item->quantity
                    ) {
                        return [
                            'success' => false,
                            'message' =>
                                "There is no longer enough available stock for {$item->product->product_name}. Physical stock: {$lockedInventory->current_stock}; already committed to other orders: {$alreadyReserved}; requested: {$item->quantity}.",
                        ];
                    }
                }

                /*
                 * Reserve the requested quantity.
                 *
                 * Physical inventory is NOT deducted here.
                 */
                foreach ($order->items as $item) {
                    $item->update([
                        'reserved_quantity' =>
                            (int) $item->quantity,
                    ]);
                }

                $order->update([
                    'status' => 'confirmed',
                    'owner_decision' => 'confirmed',
                ]);

                return [
                    'success' => true,
                    'message' =>
                        'Customer order confirmed. The requested quantities are now reserved/committed, but physical inventory was not deducted.',
                ];
            });

            if (!$result['success']) {
                return back()->withErrors([
                    'decision' =>
                        $result['message'],
                ]);
            }

            return back()->with(
                'success',
                $result['message']
            );
        }

        /*
         * ==========================================================
         * FOR PURCHASING
         * ==========================================================
         */

        if ($decision === 'for_purchasing') {
            if (
                $customerOrder->inventory_check_status !==
                'insufficient'
            ) {
                return back()->withErrors([
                    'decision' =>
                        'Proceed to Purchasing is only available when the inventory check reports insufficient stock.',
                ]);
            }

            /*
             * Make sure no old reservation remains.
             */
            $customerOrder->items()->update([
                'reserved_quantity' => 0,
            ]);

            $customerOrder->update([
                'status' => 'for_purchasing',
                'owner_decision' => 'for_purchasing',
            ]);

            return back()->with(
                'success',
                'Customer order marked for purchasing. No physical inventory was deducted and no insufficient quantity was reserved.'
            );
        }

        /*
         * ==========================================================
         * CANCEL
         * ==========================================================
         */

        if ($decision === 'cancelled') {
            /*
             * Release all reservations.
             */
            $customerOrder->items()->update([
                'reserved_quantity' => 0,
            ]);

            $customerOrder->update([
                'status' => 'cancelled',
                'owner_decision' => 'cancelled',
            ]);

            return back()->with(
                'success',
                'Customer order cancelled. Any reserved quantity was released and no physical inventory was deducted.'
            );
        }

        return back();
    }

    private function generateOrderNumber(): string
    {
        $year = now()->year;

        $lastOrder = CustomerOrder::where(
            'order_number',
            'like',
            "CO-{$year}-%"
        )
            ->orderByDesc('id')
            ->first();

        if (!$lastOrder) {
            $nextNumber = 1;
        } else {
            $lastNumber = (int) substr(
                $lastOrder->order_number,
                -4
            );

            $nextNumber = $lastNumber + 1;
        }

        return sprintf(
            'CO-%d-%04d',
            $year,
            $nextNumber
        );
    }
}