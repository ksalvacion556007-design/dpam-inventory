<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SecretaryInventoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INVENTORY PAGE
    |--------------------------------------------------------------------------
    |
    | Secretary can view:
    | - Current inventory
    | - Inventory movement history
    | - Customer Orders eligible for Stock Out
    |
    */

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | PRODUCTS
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
        | These are orders that have already been confirmed by the Owner
        | or are already in the delivery/fulfillment process.
        |
        | The order itself does NOT reduce physical inventory.
        |
        | Physical inventory is reduced only when Stock Out is recorded.
        |
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

                /*
                |--------------------------------------------------------------------------
                | Only show orders that still have an unfulfilled quantity.
                |--------------------------------------------------------------------------
                */

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
        | SEND DATA TO VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'secretary.secretary-inventory',
            compact(
                'products',
                'movements',
                'customerOrders'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STOCK IN
    |--------------------------------------------------------------------------
    |
    | Stock In is recorded only when the supplier actually delivers
    | products to DPAM.
    |
    | Creating or approving a Purchase Order does NOT increase stock.
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

            /*
            |--------------------------------------------------------------------------
            | GET OR CREATE INVENTORY
            |--------------------------------------------------------------------------
            */

            $inventory = Inventory::firstOrCreate(
                [
                    'product_id' => $validated['product_id'],
                ],
                [
                    'current_stock' => 0,
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | CALCULATE STOCK
            |--------------------------------------------------------------------------
            */

            $stockBefore =
                (int) $inventory->current_stock;

            $stockAfter =
                $stockBefore + $validated['quantity'];


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
            | RECORD STOCK IN MOVEMENT
            |--------------------------------------------------------------------------
            */

            InventoryMovement::create([
                'product_id' =>
                    $validated['product_id'],

                'user_id' =>
                    auth()->id(),

                'movement_type' =>
                    'stock_in',

                'transaction_date' =>
                    $validated['transaction_date'],

                'quantity' =>
                    $validated['quantity'],

                'stock_before' =>
                    $stockBefore,

                'stock_after' =>
                    $stockAfter,

                'supplier_customer' =>
                    $validated['supplier_customer'] ?? null,

                'unit_cost' =>
                    $validated['unit_cost'] ?? null,

                'unit_price' =>
                    null,

                'amount' =>
                    null,

                'reason' =>
                    $validated['reason']
                    ?? 'Supplier delivery',

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
                'Stock In was recorded successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | STOCK OUT
    |--------------------------------------------------------------------------
    |
    | Stock Out is recorded when products are actually released/delivered
    | to the customer and a receipt has been issued.
    |
    | Customer Order creation does NOT reduce physical inventory.
    |
    | This method:
    |
    | 1. Verifies the Customer Order.
    | 2. Verifies the selected product belongs to the order.
    | 3. Checks the quantity still needed for the order.
    | 4. Checks physical inventory.
    | 5. Reduces physical inventory.
    | 6. Increases fulfilled quantity.
    | 7. Reduces reserved quantity.
    | 8. Updates Customer Order status.
    | 9. Creates Inventory Movement / Stock Card record.
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

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER ORDER
            |--------------------------------------------------------------------------
            |
            | The form selects an actual Customer Order record.
            |
            */

            'customer_order_id' => [
                'required',
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

            /*
            |--------------------------------------------------------------------------
            | ACTUAL QUANTITY BEING RELEASED
            |--------------------------------------------------------------------------
            */

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
            |
            | Prevents two Stock Out transactions from modifying the
            | same Customer Order simultaneously.
            |
            */

            $customerOrder = CustomerOrder::where(
                'id',
                $validated['customer_order_id']
            )
                ->lockForUpdate()
                ->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | CHECK CUSTOMER ORDER STATUS
            |--------------------------------------------------------------------------
            */

            if (!in_array($customerOrder->status, [
                'confirmed',
                'ready_for_delivery',
                'partially_fulfilled',
            ])) {
                abort(
                    422,
                    'This Customer Order is not available for Stock Out.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | FIND ORDER ITEM FOR SELECTED PRODUCT
            |--------------------------------------------------------------------------
            |
            | This makes sure the selected product actually belongs
            | to the selected Customer Order.
            |
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
                abort(
                    422,
                    'The selected product does not belong to this Customer Order.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CALCULATE ORDER QUANTITY
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | Original Order Quantity = 40
            | Already Fulfilled      = 0
            | Quantity Still Needed  = 40
            |
            | If 10 were already delivered:
            |
            | Original Order Quantity = 40
            | Already Fulfilled      = 10
            | Quantity Still Needed  = 30
            |
            | This is BACKEND validation.
            | It is different from Current Inventory Stock.
            |
            */

            $fulfilledQuantity =
                (int) $orderItem->fulfilled_quantity;

            $orderedQuantity =
                (int) $orderItem->quantity;

            $remainingQuantity =
                $orderedQuantity - $fulfilledQuantity;


            /*
            |--------------------------------------------------------------------------
            | CHECK ORDER QUANTITY STILL NEEDED
            |--------------------------------------------------------------------------
            */

            if ($remainingQuantity <= 0) {
                abort(
                    422,
                    'This product has already been fully fulfilled for this Customer Order.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | THE ACTUAL STOCK OUT CANNOT EXCEED THE ORDER QUANTITY
            | STILL NEEDED.
            |--------------------------------------------------------------------------
            */

            if ($validated['quantity'] > $remainingQuantity) {
                abort(
                    422,
                    'Quantity to Stock Out cannot exceed the quantity still needed for this Customer Order.'
                );
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
                abort(
                    422,
                    'No inventory record exists for this product.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CURRENT PHYSICAL STOCK
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | Current Stock = 45
            | Customer Order Quantity = 40
            | Quantity to Stock Out = 40
            | Stock After = 5
            |
            */

            $stockBefore =
                (int) $inventory->current_stock;


            /*
            |--------------------------------------------------------------------------
            | CHECK PHYSICAL STOCK
            |--------------------------------------------------------------------------
            */

            if ($validated['quantity'] > $stockBefore) {
                abort(
                    422,
                    'Quantity to Stock Out cannot exceed the current stock.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CALCULATE STOCK AFTER DELIVERY
            |--------------------------------------------------------------------------
            */

            $stockAfter =
                $stockBefore - $validated['quantity'];


            /*
            |--------------------------------------------------------------------------
            | UPDATE PHYSICAL INVENTORY
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
                + $validated['quantity'];


            /*
            |--------------------------------------------------------------------------
            | UPDATE RESERVED QUANTITY
            |--------------------------------------------------------------------------
            |
            | Once products are actually released, the reserved quantity
            | should decrease because the reservation is now fulfilled.
            |
            */

            $currentReservedQuantity =
                (int) ($orderItem->reserved_quantity ?? 0);

            $newReservedQuantity =
                max(
                    0,
                    $currentReservedQuantity
                    - $validated['quantity']
                );


            $orderItem->update([
                'fulfilled_quantity' =>
                    $newFulfilledQuantity,

                'reserved_quantity' =>
                    $newReservedQuantity,
            ]);


            /*
            |--------------------------------------------------------------------------
            | CHECK WHETHER THE ENTIRE CUSTOMER ORDER IS FULFILLED
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


                /*
                |--------------------------------------------------------------------------
                | At least one item has been delivered.
                |--------------------------------------------------------------------------
                */

                if ($itemFulfilled > 0) {
                    $someFulfilled = true;
                }


                /*
                |--------------------------------------------------------------------------
                | At least one item is still not completely fulfilled.
                |--------------------------------------------------------------------------
                */

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

                /*
                |--------------------------------------------------------------------------
                | ALL ITEMS HAVE BEEN DELIVERED
                |--------------------------------------------------------------------------
                */

                $customerOrder->update([
                    'status' => 'fulfilled',
                ]);

            } elseif ($someFulfilled) {

                /*
                |--------------------------------------------------------------------------
                | SOME ITEMS HAVE BEEN DELIVERED
                |--------------------------------------------------------------------------
                */

                $customerOrder->update([
                    'status' => 'partially_fulfilled',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | GET PRODUCT
            |--------------------------------------------------------------------------
            */

            $product =
                Product::findOrFail(
                    $validated['product_id']
                );


            /*
            |--------------------------------------------------------------------------
            | UNIT PRICE
            |--------------------------------------------------------------------------
            |
            | If the form does not provide a price, use the product's
            | current unit price.
            |
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
                $validated['quantity']
                * $unitPrice;


            /*
            |--------------------------------------------------------------------------
            | CUSTOMER NAME
            |--------------------------------------------------------------------------
            |
            | If no customer was manually supplied, use the Customer
            | Order's customer name.
            |
            */

            $supplierCustomer =
                $validated['supplier_customer']
                ?? ($customerOrder->customer_name ?? null);


            /*
            |--------------------------------------------------------------------------
            | CREATE INVENTORY MOVEMENT
            |--------------------------------------------------------------------------
            */

            InventoryMovement::create([
                'product_id' =>
                    $validated['product_id'],

                'user_id' =>
                    auth()->id(),

                'movement_type' =>
                    'stock_out',

                'transaction_date' =>
                    $validated['transaction_date'],

                /*
                |--------------------------------------------------------------------------
                | Stock Out is recorded as a negative movement.
                |--------------------------------------------------------------------------
                */

                'quantity' =>
                    -$validated['quantity'],

                'stock_before' =>
                    $stockBefore,

                'stock_after' =>
                    $stockAfter,

                'supplier_customer' =>
                    $supplierCustomer,

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
                | Store the actual Customer Order number in Stock Card.
                |--------------------------------------------------------------------------
                */

                'customer_order_reference' =>
                    $customerOrder->order_number,

                /*
                |--------------------------------------------------------------------------
                | Receipt information.
                |--------------------------------------------------------------------------
                */

                'receipt_number' =>
                    $validated['receipt_number'],

                'received_by' =>
                    $validated['received_by'],

                'reference' =>
                    $validated['reference'] ?? null,
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | RETURN TO INVENTORY PAGE
        |--------------------------------------------------------------------------
        */

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
    | Adjustment compares the system stock with the actual
    | physical count.
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
                $actualStock - $stockBefore;


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
            | RECORD ADJUSTMENT
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


        /*
        |--------------------------------------------------------------------------
        | RETURN TO INVENTORY PAGE
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('secretary.inventory')
            ->with(
                'success',
                'Inventory adjustment was recorded successfully.'
            );
    }
}