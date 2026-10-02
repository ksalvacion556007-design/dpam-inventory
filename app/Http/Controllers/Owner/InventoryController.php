<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Payment;
use App\Models\PurchaseOrder;
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

        /*
         * Purchase Orders that can receive supplier deliveries.
         *
         * Approved:
         *     Supplier delivery has not started yet.
         *
         * Partially Received:
         *     Some ordered quantity has already been received.
         */
        $purchaseOrders = PurchaseOrder::with([
            'supplier',
            'items.product',
        ])
            ->whereIn('status', [
                'approved',
                'partially_received',
            ])
            ->orderByDesc('po_date')
            ->orderByDesc('id')
            ->get();

        return view(
            'owner.inventory',
            compact(
                'products',
                'movements',
                'customerOrders',
                'payments',
                'purchaseOrders'
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
    | Purchase Order creation does NOT increase inventory.
    |
    | Purchase Order approval does NOT increase inventory.
    |
    | Only the actual supplier delivery recorded here
    | increases physical inventory.
    |
    */

    public function stockIn(Request $request)
    {
        $validated = $request->validate([

            /*
             * The actual Purchase Order being received.
             */
            'purchase_order_id' => [
                'required',
                'integer',
                'exists:purchase_orders,id',
            ],

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

            /*
             * Lock the Purchase Order while receiving.
             *
             * This prevents two Stock In transactions
             * from updating the same PO simultaneously.
             */
            $purchaseOrder = PurchaseOrder::with([
                'supplier',
                'items.product',
            ])
                ->lockForUpdate()
                ->findOrFail(
                    $validated['purchase_order_id']
                );

            /*
             * Only Approved and Partially Received
             * Purchase Orders can receive goods.
             */
            if (
                !in_array(
                    $purchaseOrder->status,
                    [
                        'approved',
                        'partially_received',
                    ],
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'purchase_order_id' =>
                        'Only Approved or Partially Received Purchase Orders can receive Stock In.',
                ]);
            }

            /*
             * Find the selected product inside
             * the Purchase Order.
             */
            $purchaseOrderItem = $purchaseOrder->items
                ->firstWhere(
                    'product_id',
                    (int) $validated['product_id']
                );

            if (!$purchaseOrderItem) {
                throw ValidationException::withMessages([
                    'product_id' =>
                        'The selected product is not included in this Purchase Order.',
                ]);
            }

            /*
             * Ordered quantity.
             */
            $orderedQuantity =
                (int) $purchaseOrderItem->quantity;

            /*
             * Quantity already received.
             */
            $receivedQuantity =
                (int) $purchaseOrderItem->received_quantity;

            /*
             * Quantity still remaining.
             */
            $remainingQuantity =
                $orderedQuantity -
                $receivedQuantity;

            if ($remainingQuantity <= 0) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        'This Purchase Order item has already been fully received.',
                ]);
            }

            /*
             * Do not allow the supplier delivery
             * to exceed the remaining PO quantity.
             */
            if (
                (int) $validated['quantity'] >
                $remainingQuantity
            ) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        "Only {$remainingQuantity} unit(s) remain to be received for this Purchase Order item.",
                ]);
            }

            /*
             * Get the product.
             */
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

            /*
             * Lock inventory while updating it.
             */
            $inventory = Inventory::where(
                'id',
                $inventory->id
            )
                ->lockForUpdate()
                ->first();

            $stockBefore =
                (int) $inventory->current_stock;

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
             * If the user manually enters a unit cost,
             * use it.
             *
             * Otherwise use the Purchase Order item cost.
             */
            $unitCost =
                $validated['unit_cost']
                ?? $purchaseOrderItem->unit_cost;

            /*
             * Calculate Stock Card amount.
             */
            $amount = null;

            if ($unitCost !== null) {
                $amount =
                    (int) $validated['quantity'] *
                    $unitCost;
            }

            /*
             * Update the actual received quantity
             * of the Purchase Order item.
             */
            $newReceivedQuantity =
                $receivedQuantity +
                (int) $validated['quantity'];

            $purchaseOrderItem->update([
                'received_quantity' =>
                    $newReceivedQuantity,
            ]);

            /*
             * Check whether ALL Purchase Order items
             * have now been completely received.
             */
            $allItemsFullyReceived =
                $purchaseOrder->items()
                    ->whereColumn(
                        'received_quantity',
                        '<',
                        'quantity'
                    )
                    ->doesntExist();

            if ($allItemsFullyReceived) {

                /*
                 * Every item has been received.
                 */
                $purchaseOrder->update([
                    'status' => 'received',
                ]);

            } else {

                /*
                 * At least one item is still outstanding.
                 */
                $purchaseOrder->update([
                    'status' => 'partially_received',
                ]);
            }

            /*
             * Record Stock In movement in the Stock Card.
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

                /*
                 * Use the supplier from the PO
                 * when no supplier/customer was manually entered.
                 */
                'supplier_customer' =>
                    $validated['supplier_customer']
                    ?? optional($purchaseOrder->supplier)->supplier_name,

                'unit_cost' =>
                    $unitCost,

                'unit_price' =>
                    null,

                'amount' =>
                    $amount,

                'reason' =>
                    $validated['reason']
                    ?? 'Supplier delivery',

                'customer_order_reference' =>
                    null,

                'receipt_number' =>
                    null,

                'received_by' =>
                    null,

                /*
                 * Store the PO number in the Stock Card.
                 */
                'reference' =>
                    $validated['reference']
                    ?? $purchaseOrder->po_number,
            ]);
        });

        return redirect()
            ->route('owner.inventory')
            ->with(
                'success',
                'Supplier delivery recorded. Inventory and Purchase Order receiving status have been updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | STOCK OUT
    |--------------------------------------------------------------------------
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

            'customer_order_id' => [
                'required',
                'integer',
                'exists:customer_orders,id',
            ],

            'receipt_number' => [
                'required',
                'string',
                'max:255',
            ],

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

            $customerOrder = CustomerOrder::with([
                'items.product',
            ])
                ->lockForUpdate()
                ->findOrFail(
                    $validated['customer_order_id']
                );

            /*
             * Verify the Cashier receipt.
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
             * Only confirmed or partially fulfilled
             * orders can be physically released.
             */
            if (
                !in_array(
                    $customerOrder->status,
                    [
                        'confirmed',
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
             * Find the selected product in the order.
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

            $requestedQuantity =
                (int) $orderItem->quantity;

            $fulfilledQuantity =
                (int) $orderItem->fulfilled_quantity;

            $remainingQuantity =
                $requestedQuantity -
                $fulfilledQuantity;

            if ($remainingQuantity <= 0) {
                throw ValidationException::withMessages([
                    'product_id' =>
                        'This product has already been fully released for this customer order.',
                ]);
            }

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
             * Lock physical inventory.
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

            if (
                (int) $validated['quantity'] >
                $stockBefore
            ) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        "Stock Out quantity cannot be greater than current stock. Current stock: {$stockBefore}.",
                ]);
            }

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
             * Update fulfillment.
             */
            $newFulfilledQuantity =
                $fulfilledQuantity +
                (int) $validated['quantity'];

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
             * Determine overall Customer Order status.
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

                $customerOrder->update([
                    'status' => 'partially_fulfilled',
                ]);
            }

            /*
             * Product selling price.
             */
            $product = Product::findOrFail(
                $validated['product_id']
            );

            $unitPrice =
                $validated['unit_price']
                ?? $product->unit_price;

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
                 * Stock Out is stored as negative movement.
                 */
                'quantity' =>
                    -(int) $validated['quantity'],

                'stock_before' =>
                    $stockBefore,

                'stock_after' =>
                    $stockAfter,

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

                'customer_order_reference' =>
                    $customerOrder->order_number,

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

            if ($product->status === 'archived') {
                abort(
                    422,
                    'Archived products cannot be adjusted.'
                );
            }

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

            $difference =
                $stockAfter -
                $stockBefore;

            if ($difference === 0) {
                return;
            }

            $inventory->update([
                'current_stock' => $stockAfter,
            ]);

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