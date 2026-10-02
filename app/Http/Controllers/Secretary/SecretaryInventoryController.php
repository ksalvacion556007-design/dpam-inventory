<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Payment;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SecretaryInventoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INVENTORY PAGE
    |--------------------------------------------------------------------------
    |
    | Secretary can:
    | - View current inventory
    | - View inventory movement history
    | - Stock In actual supplier deliveries
    | - Stock Out actual customer deliveries
    | - Adjust physical stock
    |
    | Customer Orders do NOT reduce physical inventory.
    | Purchase Orders do NOT increase physical inventory.
    |
    */

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | ACTIVE PRODUCTS
        |--------------------------------------------------------------------------
        */

        $products = Product::with([
            'category',
            'inventory',
        ])
            ->where('status', 'active')
            ->orderBy('product_name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | INVENTORY MOVEMENTS
        |--------------------------------------------------------------------------
        */

        $movements = InventoryMovement::with([
            'product',
            'user',
        ])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER ORDERS AVAILABLE FOR STOCK OUT
        |--------------------------------------------------------------------------
        |
        | Only confirmed or partially fulfilled orders are available.
        |
        | ready_for_delivery is intentionally NOT included because it is
        | not part of the current Customer Order status flow.
        |
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
        |--------------------------------------------------------------------------
        | RECEIPTS / PAYMENTS
        |--------------------------------------------------------------------------
        |
        | Stock Out must be connected to an actual receipt created by
        | the Cashier.
        |
        | Voided receipts are excluded.
        |
        */

        $payments = Payment::with([
            'customerOrder',
        ])
            ->whereNotIn('status', [
                'voided',
            ])
            ->whereHas('customerOrder', function ($query) {
                $query->whereIn('status', [
                    'confirmed',
                    'partially_fulfilled',
                ]);
            })
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PURCHASE ORDERS AVAILABLE FOR STOCK IN
        |--------------------------------------------------------------------------
        |
        | Only Approved and Partially Received POs can receive stock.
        |
        | Draft / Pending:
        | Not yet approved, therefore cannot receive stock.
        |
        | Received:
        | Already completely received.
        |
        | Cancelled:
        | Cannot receive stock.
        |
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


        /*
        |--------------------------------------------------------------------------
        | SEND DATA TO VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'secretary.secretary-inventory',
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
    | Stock In happens only when the supplier actually delivers goods.
    |
    | Flow:
    |
    | Purchase Order
    |      ↓
    | Approved
    |      ↓
    | Supplier delivers
    |      ↓
    | Secretary records Stock In
    |      ↓
    | Inventory increases
    |      ↓
    | PO received_quantity increases
    |      ↓
    | PO becomes Partially Received or Received
    |
    */

    public function stockIn(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => [
                'required',
                'integer',
                'exists:purchase_orders,id',
            ],

            'product_id' => [
                'required',
                'integer',
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
            |--------------------------------------------------------------------------
            | LOCK PURCHASE ORDER
            |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | VERIFY PURCHASE ORDER STATUS
            |--------------------------------------------------------------------------
            */

            if (!in_array(
                $purchaseOrder->status,
                [
                    'approved',
                    'partially_received',
                ],
                true
            )) {
                throw ValidationException::withMessages([
                    'purchase_order_id' =>
                        'Only Approved or Partially Received Purchase Orders can receive Stock In.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | FIND PRODUCT INSIDE PURCHASE ORDER
            |--------------------------------------------------------------------------
            */

            $purchaseOrderItem = $purchaseOrder->items
                ->firstWhere(
                    'product_id',
                    (int) $validated['product_id']
                );


            if (!$purchaseOrderItem) {
                throw ValidationException::withMessages([
                    'product_id' =>
                        'The selected product is not included in the selected Purchase Order.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | CALCULATE REMAINING QUANTITY
            |--------------------------------------------------------------------------
            */

            $orderedQuantity = (int) $purchaseOrderItem->quantity;

            $receivedQuantity = (int) (
                $purchaseOrderItem->received_quantity ?? 0
            );

            $remainingQuantity = max(
                0,
                $orderedQuantity - $receivedQuantity
            );


            /*
            |--------------------------------------------------------------------------
            | VERIFY THERE IS STILL STOCK TO RECEIVE
            |--------------------------------------------------------------------------
            */

            if ($remainingQuantity <= 0) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        'This Purchase Order item has already been fully received.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | PREVENT OVER-RECEIVING
            |--------------------------------------------------------------------------
            */

            if (
                (int) $validated['quantity']
                > $remainingQuantity
            ) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        "Only {$remainingQuantity} unit(s) remain to be received for this Purchase Order item.",
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | GET PRODUCT
            |--------------------------------------------------------------------------
            */

            $product = Product::findOrFail(
                $validated['product_id']
            );


            if ($product->status === 'archived') {
                throw ValidationException::withMessages([
                    'product_id' =>
                        'Archived products cannot receive stock.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | GET OR CREATE INVENTORY
            |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | LOCK INVENTORY
            |--------------------------------------------------------------------------
            */

            $inventory = Inventory::where(
                'id',
                $inventory->id
            )
                ->lockForUpdate()
                ->first();


            /*
            |--------------------------------------------------------------------------
            | CALCULATE STOCK
            |--------------------------------------------------------------------------
            */

            $stockBefore = (int) $inventory->current_stock;

            $quantityReceived = (int) $validated['quantity'];

            $stockAfter =
                $stockBefore
                + $quantityReceived;


            /*
            |--------------------------------------------------------------------------
            | UPDATE INVENTORY
            |--------------------------------------------------------------------------
            */

            $inventory->update([
                'current_stock' => $stockAfter,
            ]);


            /*
            |--------------------------------------------------------------------------
            | UNIT COST
            |--------------------------------------------------------------------------
            |
            | If Secretary leaves Unit Cost blank,
            | use the Purchase Order unit cost.
            |
            */

            $unitCost = $validated['unit_cost']
                ?? $purchaseOrderItem->unit_cost;


            /*
            |--------------------------------------------------------------------------
            | CALCULATE AMOUNT
            |--------------------------------------------------------------------------
            */

            $amount = $unitCost !== null
                ? $quantityReceived * (float) $unitCost
                : null;


            /*
            |--------------------------------------------------------------------------
            | UPDATE PO RECEIVED QUANTITY
            |--------------------------------------------------------------------------
            */

            $newReceivedQuantity =
                $receivedQuantity
                + $quantityReceived;


            $purchaseOrderItem->update([
                'received_quantity' =>
                    $newReceivedQuantity,
            ]);


            /*
            |--------------------------------------------------------------------------
            | DETERMINE PURCHASE ORDER STATUS
            |--------------------------------------------------------------------------
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

                $purchaseOrder->update([
                    'status' => 'received',
                ]);

            } else {

                $purchaseOrder->update([
                    'status' => 'partially_received',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | CREATE STOCK CARD MOVEMENT
            |--------------------------------------------------------------------------
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
                    $quantityReceived,

                'stock_before' =>
                    $stockBefore,

                'stock_after' =>
                    $stockAfter,

                /*
                |--------------------------------------------------------------------------
                | Supplier comes from the Purchase Order.
                |--------------------------------------------------------------------------
                */

                'supplier_customer' =>
                    optional(
                        $purchaseOrder->supplier
                    )->supplier_name,

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
                |--------------------------------------------------------------------------
                | PO number becomes the default reference.
                |--------------------------------------------------------------------------
                */

                'reference' =>
                    $validated['reference']
                    ?? $purchaseOrder->po_number,
            ]);
        });


        return redirect()
            ->route('secretary.inventory')
            ->with(
                'success',
                'Supplier delivery recorded. Inventory and Purchase Order receiving status have been updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | STOCK OUT
    |--------------------------------------------------------------------------
    |
    | Stock Out happens only when products are actually released to the
    | customer.
    |
    | Customer Order creation does NOT reduce inventory.
    |
    | A real Cashier receipt must exist before Stock Out.
    |
    */

    public function stockOut(Request $request)
    {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'transaction_date' => [
                'required',
                'date',
            ],

            'customer_order_id' => [
                'required',
                'integer',
                'exists:customer_orders,id',
            ],

            'payment_id' => [
                'required',
                'integer',
                'exists:payments,id',
            ],

            'received_by' => [
                'required',
                'string',
                'max:255',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
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

            /*
            |--------------------------------------------------------------------------
            | LOCK CUSTOMER ORDER
            |--------------------------------------------------------------------------
            */

            $customerOrder = CustomerOrder::where(
                'id',
                $validated['customer_order_id']
            )
                ->lockForUpdate()
                ->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | VERIFY CUSTOMER ORDER STATUS
            |--------------------------------------------------------------------------
            */

            if (!in_array(
                $customerOrder->status,
                [
                    'confirmed',
                    'partially_fulfilled',
                ],
                true
            )) {
                throw ValidationException::withMessages([
                    'customer_order_id' =>
                        'This Customer Order is not available for Stock Out.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | VERIFY RECEIPT
            |--------------------------------------------------------------------------
            |
            | The selected receipt must:
            | - exist
            | - belong to the selected Customer Order
            | - not be voided
            |
            */

            $payment = Payment::where(
                'id',
                $validated['payment_id']
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
                    'payment_id' =>
                        'The selected receipt does not belong to this Customer Order or is no longer valid.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | FIND CUSTOMER ORDER ITEM
            |--------------------------------------------------------------------------
            */

            $orderItem = CustomerOrderItem::where(
                'customer_order_id',
                $customerOrder->id
            )
                ->where(
                    'product_id',
                    $validated['product_id']
                )
                ->lockForUpdate()
                ->first();


            if (!$orderItem) {
                throw ValidationException::withMessages([
                    'product_id' =>
                        'The selected product does not belong to this Customer Order.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | CALCULATE REMAINING ORDER QUANTITY
            |--------------------------------------------------------------------------
            */

            $fulfilledQuantity =
                (int) $orderItem->fulfilled_quantity;

            $orderedQuantity =
                (int) $orderItem->quantity;

            $remainingQuantity =
                $orderedQuantity
                - $fulfilledQuantity;


            if ($remainingQuantity <= 0) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        'This product has already been fully fulfilled for this Customer Order.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | PREVENT OVER-FULFILLMENT
            |--------------------------------------------------------------------------
            */

            if (
                (int) $validated['quantity']
                > $remainingQuantity
            ) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        'Quantity to Stock Out cannot exceed the quantity still needed for this Customer Order.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | GET INVENTORY
            |--------------------------------------------------------------------------
            */

            $inventory = Inventory::where(
                'product_id',
                $validated['product_id']
            )
                ->lockForUpdate()
                ->first();


            if (!$inventory) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        'No inventory record exists for this product.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | CHECK CURRENT STOCK
            |--------------------------------------------------------------------------
            */

            $stockBefore =
                (int) $inventory->current_stock;

            $quantityReleased =
                (int) $validated['quantity'];


            if ($quantityReleased > $stockBefore) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        'Quantity to Stock Out cannot exceed the current stock.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | CALCULATE STOCK AFTER
            |--------------------------------------------------------------------------
            */

            $stockAfter =
                $stockBefore
                - $quantityReleased;


            /*
            |--------------------------------------------------------------------------
            | UPDATE INVENTORY
            |--------------------------------------------------------------------------
            */

            $inventory->update([
                'current_stock' =>
                    $stockAfter,
            ]);


            /*
            |--------------------------------------------------------------------------
            | UPDATE FULFILLED QUANTITY
            |--------------------------------------------------------------------------
            */

            $newFulfilledQuantity =
                $fulfilledQuantity
                + $quantityReleased;


            /*
            |--------------------------------------------------------------------------
            | UPDATE RESERVED QUANTITY
            |--------------------------------------------------------------------------
            */

            $currentReservedQuantity =
                (int) (
                    $orderItem->reserved_quantity ?? 0
                );


            $newReservedQuantity = max(
                0,
                $currentReservedQuantity
                - $quantityReleased
            );


            $orderItem->update([
                'fulfilled_quantity' =>
                    $newFulfilledQuantity,

                'reserved_quantity' =>
                    $newReservedQuantity,
            ]);


            /*
            |--------------------------------------------------------------------------
            | CHECK WHOLE CUSTOMER ORDER
            |--------------------------------------------------------------------------
            */

            $allItems = CustomerOrderItem::where(
                'customer_order_id',
                $customerOrder->id
            )->get();


            $allFulfilled = true;
            $someFulfilled = false;


            foreach ($allItems as $item) {

                $itemFulfilled =
                    (int) $item->fulfilled_quantity;

                $itemQuantity =
                    (int) $item->quantity;


                if ($itemFulfilled > 0) {
                    $someFulfilled = true;
                }


                if ($itemFulfilled < $itemQuantity) {
                    $allFulfilled = false;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE CUSTOMER ORDER STATUS
            |--------------------------------------------------------------------------
            */

            if ($allFulfilled) {

                $customerOrder->update([
                    'status' => 'fulfilled',
                ]);

            } elseif ($someFulfilled) {

                $customerOrder->update([
                    'status' => 'partially_fulfilled',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | GET PRODUCT
            |--------------------------------------------------------------------------
            */

            $product = Product::findOrFail(
                $validated['product_id']
            );


            /*
            |--------------------------------------------------------------------------
            | UNIT PRICE
            |--------------------------------------------------------------------------
            */

            $unitPrice =
                $validated['unit_price']
                ?? $product->unit_price;


            /*
            |--------------------------------------------------------------------------
            | CALCULATE AMOUNT
            |--------------------------------------------------------------------------
            */

            $amount =
                $quantityReleased
                * (float) $unitPrice;


            /*
            |--------------------------------------------------------------------------
            | CREATE STOCK CARD MOVEMENT
            |--------------------------------------------------------------------------
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
                |--------------------------------------------------------------------------
                | Stock Out is stored as a negative movement.
                |--------------------------------------------------------------------------
                */

                'quantity' =>
                    -$quantityReleased,

                'stock_before' =>
                    $stockBefore,

                'stock_after' =>
                    $stockAfter,

                'supplier_customer' =>
                    $customerOrder->customer_name,

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
                |--------------------------------------------------------------------------
                | Customer Order number.
                |--------------------------------------------------------------------------
                */

                'customer_order_reference' =>
                    $customerOrder->order_number,

                /*
                |--------------------------------------------------------------------------
                | Actual Cashier receipt number.
                |--------------------------------------------------------------------------
                */

                'receipt_number' =>
                    $payment->receipt_number,

                /*
                |--------------------------------------------------------------------------
                | Person who physically received the goods.
                |--------------------------------------------------------------------------
                */

                'received_by' =>
                    $validated['received_by'],

                'reference' =>
                    $validated['reference'] ?? null,
            ]);
        });


        return redirect()
            ->route('secretary.inventory')
            ->with(
                'success',
                'Stock Out was recorded successfully. Inventory and Customer Order quantities were updated.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | STOCK ADJUSTMENT
    |--------------------------------------------------------------------------
    |
    | Adjustment is used when the physical count does not match the
    | system inventory.
    |
    */

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'integer',
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

            /*
            |--------------------------------------------------------------------------
            | GET OR CREATE INVENTORY
            |--------------------------------------------------------------------------
            */

            $inventory = Inventory::firstOrCreate(
                [
                    'product_id' =>
                        $validated['product_id'],
                ],
                [
                    'current_stock' =>
                        0,
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | LOCK INVENTORY
            |--------------------------------------------------------------------------
            */

            $inventory = Inventory::where(
                'id',
                $inventory->id
            )
                ->lockForUpdate()
                ->first();


            /*
            |--------------------------------------------------------------------------
            | CURRENT SYSTEM STOCK
            |--------------------------------------------------------------------------
            */

            $stockBefore =
                (int) $inventory->current_stock;


            /*
            |--------------------------------------------------------------------------
            | ACTUAL PHYSICAL COUNT
            |--------------------------------------------------------------------------
            */

            $actualStock =
                (int) $validated['actual_stock'];


            /*
            |--------------------------------------------------------------------------
            | CALCULATE DIFFERENCE
            |--------------------------------------------------------------------------
            */

            $difference =
                $actualStock
                - $stockBefore;


            /*
            |--------------------------------------------------------------------------
            | UPDATE INVENTORY
            |--------------------------------------------------------------------------
            */

            $inventory->update([
                'current_stock' =>
                    $actualStock,
            ]);


            /*
            |--------------------------------------------------------------------------
            | CREATE ADJUSTMENT MOVEMENT
            |--------------------------------------------------------------------------
            */

            InventoryMovement::create([
                'product_id' =>
                    $validated['product_id'],

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
                    $actualStock,

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
                    $validated['reference'] ?? null,
            ]);
        });


        return redirect()
            ->route('secretary.inventory')
            ->with(
                'success',
                'Inventory adjustment was recorded successfully.'
            );
    }
}