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
        /*
         * EXISTING PURCHASE ORDERS
         *
         * Always load the supplier relationship.
         *
         * This is important because a supplier may become inactive
         * after an existing Purchase Order was created.
         *
         * Historical Purchase Orders must still show their supplier.
         */
        $purchaseOrders = PurchaseOrder::with([
            'supplier',
            'customerOrder',
            'items.product',
            'user',
        ])
            ->latest('po_date')
            ->latest('id')
            ->get();

        /*
         * NEW PURCHASE ORDERS
         *
         * Only active suppliers may be selected.
         */
        $suppliers = Supplier::query()
            ->where('status', 'active')
            ->orderBy('supplier_name')
            ->get();

        /*
         * Only active products can be selected for NEW POs.
         *
         * Products remain available even if one of their suppliers
         * becomes inactive.
         *
         * The supplier relationship is loaded only if the Product model
         * already has the suppliers() many-to-many relationship.
         */
        $products = Product::query()
            ->where('status', 'active')
            ->with([
                'inventory',
                'category',
            ])
            ->orderBy('product_name')
            ->get();

        /*
         * Customer Orders that may require purchasing.
         */
        $customerOrders = CustomerOrder::query()
            ->whereIn('status', [
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
    | SHARED VALIDATION RULES
    |--------------------------------------------------------------------------
    */

    private function rules(array $supplierRule): array
    {
        return [

            /*
             * Supplier must be active for a NEW PO.
             */
            'supplier_id' => $supplierRule,

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

            /*
             * Purchase Unit Cost belongs to the PO item.
             *
             * It is NOT the Product Selling Price.
             */
            'items.*.unit_cost' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION MESSAGES
    |--------------------------------------------------------------------------
    */

    private const MESSAGES = [
        'supplier_id.exists' =>
            'The selected supplier is inactive or does not exist. Please choose an active supplier.',
    ];


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->rules([
                'required',
                'integer',

                /*
                 * NEW Purchase Orders may only use active suppliers.
                 */
                Rule::exists('suppliers', 'id')
                    ->where('status', 'active'),
            ]),
            self::MESSAGES
        );

        DB::transaction(function () use ($validated) {

            $purchaseOrder = PurchaseOrder::create([
                'po_number' =>
                    'TEMP-' . Str::uuid(),

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
             * Create PO Items.
             */
            foreach ($validated['items'] as $item) {

                $product = Product::findOrFail(
                    $item['product_id']
                );


                /*
                 * Only active products may be added to NEW POs.
                 */
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
        /*
         * Always load the supplier, including inactive suppliers.
         *
         * This preserves historical PO information.
         */
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

        /*
         * Only Draft or Pending POs can be edited.
         */
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


        $validated = $request->validate(
            $this->rules([
                'required',
                'integer',

                /*
                 * Normally only ACTIVE suppliers can be selected.
                 *
                 * However, if the PO already uses a supplier that
                 * later became inactive, allow that existing supplier
                 * to remain attached to this PO.
                 *
                 * This prevents an existing PO from being broken.
                 */
                Rule::exists('suppliers', 'id')
                    ->where(function ($query) use ($purchaseOrder) {

                        $query
                            ->where('status', 'active')
                            ->orWhere(
                                'id',
                                $purchaseOrder->supplier_id
                            );
                    }),
            ]),
            self::MESSAGES
        );


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


            /*
             * Rebuild PO items.
             */
            $purchaseOrder->items()->delete();


            foreach ($validated['items'] as $item) {

                $product = Product::findOrFail(
                    $item['product_id']
                );


                /*
                 * Inactive products cannot be added to a PO.
                 */
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