<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PurchaseOrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $purchaseOrders = PurchaseOrder::with([
            'supplier',
            'customerOrder',
            'items.product',
            'user',
        ])
            ->latest('po_date')
            ->latest('id')
            ->get();

        $suppliers = Supplier::where('status', 'active')
            ->orderBy('supplier_name')
            ->get();

        $products = Product::where('status', 'active')
            ->with('inventory')
            ->orderBy('product_name')
            ->get();

        $customerOrders = CustomerOrder::whereIn('status', [
            'for_purchasing',
            'pending_inventory_check',
        ])
            ->orderByDesc('order_date')
            ->get();

        return view('owner.purchase-orders', compact(
            'purchaseOrders',
            'suppliers',
            'products',
            'customerOrders'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'integer',
                'exists:suppliers,id',
            ],

            'customer_order_id' => [
                'nullable',
                'integer',
                'exists:customer_orders,id',
            ],

            'po_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'pending',
                ]),
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
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

            'items.*.unit_cost' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);


        DB::transaction(function () use ($validated) {

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => 'TEMP-' . Str::uuid(),

                'supplier_id' =>
                    $validated['supplier_id'],

                'customer_order_id' =>
                    $validated['customer_order_id'] ?? null,

                'po_date' =>
                    $validated['po_date'],

                'status' =>
                    $validated['status'],

                'notes' =>
                    $validated['notes'] ?? null,

                'user_id' =>
                    auth()->id(),
            ]);


            /*
             * Generate permanent PO number.
             */

            $purchaseOrder->update([
                'po_number' => sprintf(
                    'PO-%d-%04d',
                    now()->year,
                    $purchaseOrder->id
                ),
            ]);


            /*
             * Create Purchase Order Items.
             */

            foreach ($validated['items'] as $item) {

                $product = Product::findOrFail(
                    $item['product_id']
                );


                if ($product->status !== 'active') {

                    abort(
                        422,
                        'Only active products can be added to a Purchase Order.'
                    );
                }


                $quantity = (int) $item['quantity'];

                $unitCost = (float) $item['unit_cost'];

                $subtotal = round(
                    $quantity * $unitCost,
                    2
                );


                PurchaseOrderItem::create([
                    'purchase_order_id' =>
                        $purchaseOrder->id,

                    'product_id' =>
                        $product->id,

                    'quantity' =>
                        $quantity,

                    'unit_cost' =>
                        $unitCost,

                    'subtotal' =>
                        $subtotal,
                ]);
            }
        });


        return redirect()
            ->route('owner.purchase-orders')
            ->with(
                'success',
                'Purchase Order created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'supplier',
            'customerOrder',
            'items.product',
            'user',
        ]);

        return response()->json([
            'purchase_order' => $purchaseOrder,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        PurchaseOrder $purchaseOrder
    ) {
        if (!in_array(
            $purchaseOrder->status,
            ['draft', 'pending'],
            true
        )) {
            return back()->withErrors([
                'purchase_order' =>
                    'Only Draft or Pending Purchase Orders can be edited.',
            ]);
        }


        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'integer',
                'exists:suppliers,id',
            ],

            'customer_order_id' => [
                'nullable',
                'integer',
                'exists:customer_orders,id',
            ],

            'po_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'pending',
                ]),
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
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

            'items.*.unit_cost' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);


        DB::transaction(function () use (
            $validated,
            $purchaseOrder
        ) {

            $purchaseOrder->update([
                'supplier_id' =>
                    $validated['supplier_id'],

                'customer_order_id' =>
                    $validated['customer_order_id'] ?? null,

                'po_date' =>
                    $validated['po_date'],

                'status' =>
                    $validated['status'],

                'notes' =>
                    $validated['notes'] ?? null,
            ]);


            $purchaseOrder->items()->delete();


            foreach ($validated['items'] as $item) {

                $product = Product::findOrFail(
                    $item['product_id']
                );


                if ($product->status !== 'active') {

                    abort(
                        422,
                        'Only active products can be added to a Purchase Order.'
                    );
                }


                $quantity = (int) $item['quantity'];

                $unitCost = (float) $item['unit_cost'];


                PurchaseOrderItem::create([
                    'purchase_order_id' =>
                        $purchaseOrder->id,

                    'product_id' =>
                        $product->id,

                    'quantity' =>
                        $quantity,

                    'unit_cost' =>
                        $unitCost,

                    'subtotal' =>
                        round(
                            $quantity * $unitCost,
                            2
                        ),
                ]);
            }
        });


        return redirect()
            ->route('owner.purchase-orders')
            ->with(
                'success',
                'Purchase Order updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SUBMIT FOR APPROVAL
    |--------------------------------------------------------------------------
    */

    public function submit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {

            return back()->withErrors([
                'purchase_order' =>
                    'Only Draft Purchase Orders can be submitted for approval.',
            ]);
        }

        $purchaseOrder->update([
            'status' => 'pending',
        ]);

        return redirect()
            ->route('owner.purchase-orders')
            ->with(
                'success',
                'Purchase Order submitted for Owner approval successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */

    public function approve(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {

            return back()->withErrors([
                'purchase_order' =>
                    'Only Pending Purchase Orders can be approved.',
            ]);
        }


        $purchaseOrder->update([
            'status' => 'approved',
        ]);


        return redirect()
            ->route('owner.purchase-orders')
            ->with(
                'success',
                'Purchase Order approved successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if (in_array(
            $purchaseOrder->status,
            ['received', 'partially_received'],
            true
        )) {
            return back()->withErrors([
                'purchase_order' =>
                    'A Purchase Order that has already received goods cannot be cancelled.',
            ]);
        }


        if ($purchaseOrder->status === 'cancelled') {

            return back()->withErrors([
                'purchase_order' =>
                    'This Purchase Order is already cancelled.',
            ]);
        }


        $purchaseOrder->update([
            'status' => 'cancelled',
        ]);


        return redirect()
            ->route('owner.purchase-orders')
            ->with(
                'success',
                'Purchase Order cancelled.'
            );
    }
}