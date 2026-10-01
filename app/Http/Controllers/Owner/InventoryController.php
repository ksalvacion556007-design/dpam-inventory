<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INVENTORY PAGE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $products = Product::with([
            'category',
            'inventory',
            'inventoryMovements.user',
        ])
            ->where('status', '!=', 'archived')
            ->orderBy('product_name')
            ->get();

        $movements = InventoryMovement::with([
            'product',
            'user',
        ])
            ->latest()
            ->get();

        /*
         * Customer Orders that still have products
         * waiting to be physically released.
         */
        $customerOrders = CustomerOrder::with([
            'items.product',
        ])
            ->whereIn('status', [
                'confirmed',
                'ready_for_delivery',
                'partially_fulfilled',
            ])
            ->whereHas('items', function ($query) {
                $query->whereColumn(
                    'fulfilled_quantity',
                    '<',
                    'quantity'
                );
            })
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->get();

        /*
         * Cashier-created receipts.
         *
         * These receipts are used by Owner Stock Out.
         *
         * Voided receipts cannot be used for Stock Out.
         */
        $payments = Payment::with([
            'customerOrder',
        ])
            ->where('status', '!=', 'voided')
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        return view(
            'owner.inventory',
            compact(
                'products',
                'movements',
                'customerOrders',
                'payments'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STOCK IN
    |--------------------------------------------------------------------------
    |
    | Stock In represents the actual delivery of products
    | from a supplier.
    |
    | Creating or approving a Purchase Order does NOT
    | automatically increase inventory.
    |
    | Inventory increases only when the actual supplier
    | delivery is received and recorded here.
    |
    */

    public function stockIn(Request $request)
    {
        $validated = $request->validate([

            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'transaction_date' => [
                'required',
                'date',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'supplier_customer' => [
                'nullable',
                'string',
                'max:255',
            ],

            'unit_cost' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:255',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            $product = Product::findOrFail(
                $validated['product_id']
            );

            /*
             * Archived products cannot receive stock.
             */
            if ($product->status === 'archived') {
                abort(
                    422,
                    'Archived products cannot receive stock.'
                );
            }

            /*
             * Find or create the inventory record.
             */
            $inventory = Inventory::firstOrCreate(
                [
                    'product_id' => $product->id,
                ],
                [
                    'current_stock' => 0,
                ]
            );

            $stockBefore = (int) $inventory->current_stock;

            $stockAfter =
                $stockBefore +
                (int) $validated['quantity'];

            /*
             * Update physical inventory.
             */
            $inventory->update([
                'current_stock' => $stockAfter,
            ]);

            /*
             * Calculate Stock In amount when unit cost
             * is supplied.
             */
            $unitCost =
                $validated['unit_cost'] ?? null;

            $amount = null;

            if ($unitCost !== null) {
                $amount =
                    (int) $validated['quantity'] *
                    $unitCost;
            }

            /*
             * Record Stock In movement.
             */
            InventoryMovement::create([

                'product_id' =>
                    $product->id,

                'user_id' =>
                    auth()->id(),

                'movement_type' =>
                    'stock_in',

                'transaction_date' =>
                    $validated['transaction_date'],

                'quantity' =>
                    (int) $validated['quantity'],

                'stock_before' =>
                    $stockBefore,

                'stock_after' =>
                    $stockAfter,

                'supplier_customer' =>
                    $validated['supplier_customer']
                    ?? null,

                'unit_cost' =>
                    $unitCost,

                'unit_price' =>
                    null,

                'amount' =>
                    $amount,

                'reason' =>
                    $validated['reason']
                    ?? null,

                'customer_order_reference' =>
                    null,

                'receipt_number' =>
                    null,

                'received_by' =>
                    null,

                'reference' =>
                    $validated['reference']
                    ?? null,
            ]);
        });

        return redirect()
            ->route('owner.inventory')
            ->with(
                'success',
                'Stock added successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | STOCK OUT
    |--------------------------------------------------------------------------
    |
    | Stock Out represents the actual customer delivery / release.
    |
    | Flow:
    |
    | Customer Order
    |       ↓
    | Secretary checks inventory
    |       ↓
    | Owner confirms order
    |       ↓
    | Cashier creates/issues receipt
    |       ↓
    | Actual delivery / release
    |       ↓
    | Owner records Stock Out
    |       ↓
    | Physical inventory decreases
    |       ↓
    | Customer Order fulfillment is updated
    |
    | IMPORTANT:
    |
    | Customer Order creation does NOT deduct inventory.
    |
    | Payment does NOT deduct inventory.
    |
    | Stock Out is what deducts the physical inventory.
    |
    */

    public function stockOut(Request $request)
    {
        $validated = $request->validate([

            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'transaction_date' => [
                'required',
                'date',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            /*
             * Actual Customer Order ID selected
             * from the Customer Order dropdown.
             */
            'customer_order_id' => [
                'required',
                'integer',
                'exists:customer_orders,id',
            ],

            /*
             * Receipt number created by Cashier.
             *
             * The receipt is verified below against
             * the payments table.
             */
            'receipt_number' => [
                'required',
                'string',
                'max:255',
            ],

            /*
             * Person who received the products.
             */
            'received_by' => [
                'required',
                'string',
                'max:255',
            ],

            'supplier_customer' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
             * Optional override of the product selling price.
             *
             * If not supplied, the Product unit price
             * will automatically be used.
             */
            'unit_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:255',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            /*
             * Lock the Customer Order so that two
             * Stock Out transactions cannot fulfill
             * the same quantity simultaneously.
             */
            $customerOrder = CustomerOrder::with([
                'items.product',
            ])
                ->lockForUpdate()
                ->findOrFail(
                    $validated['customer_order_id']
                );

            /*
             * Verify that the receipt actually exists
             * in the Cashier payments table.
             *
             * The receipt must:
             *
             * 1. Exist
             * 2. Belong to this Customer Order
             * 3. Not be voided
             */
            $payment = Payment::where(
                'receipt_number',
                $validated['receipt_number']
            )
                ->where(
                    'customer_order_id',
                    $customerOrder->id
                )
                ->where(
                    'status',
                    '!=',
                    'voided'
                )
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                throw ValidationException::withMessages([
                    'receipt_number' =>
                        'The selected receipt does not exist, does not belong to this customer order, or has been voided.',
                ]);
            }

            /*
             * Only confirmed / delivery-ready orders
             * can be physically released.
             */
            if (
                !in_array(
                    $customerOrder->status,
                    [
                        'confirmed',
                        'ready_for_delivery',
                        'partially_fulfilled',
                    ],
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'customer_order_id' =>
                        'Only confirmed customer orders can be released through Stock Out.',
                ]);
            }

            /*
             * Find the selected product inside
             * the selected Customer Order.
             */
            $orderItem = $customerOrder->items
                ->firstWhere(
                    'product_id',
                    (int) $validated['product_id']
                );

            if (!$orderItem) {
                throw ValidationException::withMessages([
                    'product_id' =>
                        'The selected product is not part of the selected customer order.',
                ]);
            }

            /*
             * Determine how much of this order item
             * has already been fulfilled.
             */
            $requestedQuantity =
                (int) $orderItem->quantity;

            $fulfilledQuantity =
                (int) $orderItem->fulfilled_quantity;

            /*
             * Quantity still needed for this
             * Customer Order item.
             */
            $remainingQuantity =
                $requestedQuantity -
                $fulfilledQuantity;

            if ($remainingQuantity <= 0) {
                throw ValidationException::withMessages([
                    'product_id' =>
                        'This product has already been fully released for this customer order.',
                ]);
            }

            /*
             * Stock Out cannot exceed the quantity
             * still needed by the Customer Order.
             */
            if (
                (int) $validated['quantity'] >
                $remainingQuantity
            ) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        "Only {$remainingQuantity} unit(s) are still needed for this product in the selected customer order.",
                ]);
            }

            /*
             * Get and lock the physical inventory record.
             */
            $inventory = Inventory::where(
                'product_id',
                $validated['product_id']
            )
                ->lockForUpdate()
                ->first();

            if (!$inventory) {
                throw ValidationException::withMessages([
                    'product_id' =>
                        'No inventory record exists for the selected product.',
                ]);
            }

            $stockBefore =
                (int) $inventory->current_stock;

            /*
             * Prevent negative physical inventory.
             */
            if (
                (int) $validated['quantity'] >
                $stockBefore
            ) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        "Stock Out quantity cannot be greater than current stock. Current stock: {$stockBefore}.",
                ]);
            }

            /*
             * Calculate the new physical stock.
             */
            $stockAfter =
                $stockBefore -
                (int) $validated['quantity'];

            /*
             * Update physical inventory.
             */
            $inventory->update([
                'current_stock' => $stockAfter,
            ]);

            /*
             * Update Customer Order fulfillment.
             */
            $newFulfilledQuantity =
                $fulfilledQuantity +
                (int) $validated['quantity'];

            /*
             * Reserved quantity decreases when
             * reserved products are physically released.
             */
            $newReservedQuantity = max(
                0,
                (int) $orderItem->reserved_quantity -
                (int) $validated['quantity']
            );

            $orderItem->update([
                'fulfilled_quantity' =>
                    $newFulfilledQuantity,

                'reserved_quantity' =>
                    $newReservedQuantity,
            ]);

            /*
             * Check whether all items in the
             * Customer Order have been fulfilled.
             */
            $customerOrder->refresh();

            $allItemsFulfilled =
                $customerOrder->items()
                    ->whereColumn(
                        'fulfilled_quantity',
                        '<',
                        'quantity'
                    )
                    ->doesntExist();

            if ($allItemsFulfilled) {

                $customerOrder->update([
                    'status' => 'fulfilled',
                ]);

            } else {

                /*
                 * At least one item still needs to be fulfilled.
                 */
                $customerOrder->update([
                    'status' => 'partially_fulfilled',
                ]);
            }

            /*
             * Get the Product so that its unit price
             * can be used when the form does not provide
             * a specific unit price.
             */
            $product = Product::findOrFail(
                $validated['product_id']
            );

            /*
             * Use the supplied unit price if provided.
             * Otherwise use the Product's current unit price.
             */
            $unitPrice =
                $validated['unit_price']
                ?? $product->unit_price;

            /*
             * Calculate Stock Card amount.
             */
            $amount =
                (int) $validated['quantity'] *
                $unitPrice;

            /*
             * Record Stock Out movement.
             */
            InventoryMovement::create([

                'product_id' =>
                    $product->id,

                'user_id' =>
                    auth()->id(),

                'movement_type' =>
                    'stock_out',

                'transaction_date' =>
                    $validated['transaction_date'],

                /*
                 * Negative quantity means physical
                 * inventory is leaving DPAM.
                 */
                'quantity' =>
                    -(int) $validated['quantity'],

                'stock_before' =>
                    $stockBefore,

                'stock_after' =>
                    $stockAfter,

                /*
                 * If supplier_customer was not manually
                 * supplied, use the Customer Order customer.
                 */
                'supplier_customer' =>
                    $validated['supplier_customer']
                    ?? $customerOrder->customer_name,

                'unit_cost' =>
                    null,

                'unit_price' =>
                    $unitPrice,

                'amount' =>
                    $amount,

                'reason' =>
                    $validated['reason']
                    ?? 'Customer delivery / release',

                /*
                 * Store the actual Customer Order number
                 * in the Stock Card.
                 */
                'customer_order_reference' =>
                    $customerOrder->order_number,

                /*
                 * Store the verified Cashier receipt number.
                 */
                'receipt_number' =>
                    $validated['receipt_number'],

                'received_by' =>
                    $validated['received_by'],

                'reference' =>
                    $validated['reference']
                    ?? null,
            ]);
        });

        return redirect()
            ->route('owner.inventory')
            ->with(
                'success',
                'Customer delivery recorded. Physical inventory and customer order fulfillment have been updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ADJUST STOCK
    |--------------------------------------------------------------------------
    |
    | Adjust Stock is used when the actual physical count
    | differs from the system inventory.
    |
    */

    public function adjust(Request $request)
    {
        $validated = $request->validate([

            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'transaction_date' => [
                'required',
                'date',
            ],

            'actual_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'reason' => [
                'required',
                'string',
                'max:255',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            $product = Product::findOrFail(
                $validated['product_id']
            );

            /*
             * Archived products cannot be adjusted.
             */
            if ($product->status === 'archived') {
                abort(
                    422,
                    'Archived products cannot be adjusted.'
                );
            }

            /*
             * Find or create inventory record.
             */
            $inventory = Inventory::firstOrCreate(
                [
                    'product_id' => $product->id,
                ],
                [
                    'current_stock' => 0,
                ]
            );

            $stockBefore =
                (int) $inventory->current_stock;

            $stockAfter =
                (int) $validated['actual_stock'];

            /*
             * Positive difference = stock increase.
             * Negative difference = stock decrease.
             */
            $difference =
                $stockAfter -
                $stockBefore;

            /*
             * No movement is necessary if the
             * physical count matches the system count.
             */
            if ($difference === 0) {
                return;
            }

            /*
             * Update physical inventory.
             */
            $inventory->update([
                'current_stock' => $stockAfter,
            ]);

            /*
             * Record adjustment in Stock Card.
             */
            InventoryMovement::create([

                'product_id' =>
                    $product->id,

                'user_id' =>
                    auth()->id(),

                'movement_type' =>
                    'adjustment',

                'transaction_date' =>
                    $validated['transaction_date'],

                'quantity' =>
                    $difference,

                'stock_before' =>
                    $stockBefore,

                'stock_after' =>
                    $stockAfter,

                'supplier_customer' =>
                    null,

                'unit_cost' =>
                    null,

                'unit_price' =>
                    null,

                'amount' =>
                    null,

                'reason' =>
                    $validated['reason'],

                'customer_order_reference' =>
                    null,

                'receipt_number' =>
                    null,

                'received_by' =>
                    null,

                'reference' =>
                    $validated['reference']
                    ?? null,
            ]);
        });

        return redirect()
            ->route('owner.inventory')
            ->with(
                'success',
                'Stock adjustment recorded successfully.'
            );
    }
}