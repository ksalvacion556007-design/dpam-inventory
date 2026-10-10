<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\Inventory;
use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\SalesService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SalesInventoryController extends Controller
{
    public function __construct(
        private SalesService $sales
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | MAIN PAGE
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $products = Product::with([
                'category',
                'inventory',
                'suppliers',
                'inventoryBatches' => function ($query) {
                    $query
                        ->where('quantity_remaining', '>', 0)
                        ->orderByRaw(
                            'COALESCE(expiration_date, best_before_date) ASC'
                        )
                        ->orderBy('received_date');
                },
            ])
            ->withReservedTotal()
            ->orderBy('product_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CATEGORIES
        |--------------------------------------------------------------------------
        */

        $categories = Category::orderBy('category_name')->get();

        /*
        |--------------------------------------------------------------------------
        | ACTIVE SUPPLIERS
        |--------------------------------------------------------------------------
        */

        $suppliers = Supplier::active()
            ->orderBy('supplier_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ORDERS
        |--------------------------------------------------------------------------
        */

        $orders = CustomerOrder::with([
                'items.product',
                'payments',
                'inventoryCheckedBy',
                'voidedBy',
            ])
            ->latest('order_date')
            ->latest('id')
            ->limit(500)
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
            ->latest('id')
            ->limit(1000)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PURCHASE ORDERS ELIGIBLE FOR STOCK IN
        |--------------------------------------------------------------------------
        */

        $purchaseOrders = PurchaseOrder::with([
                'supplier',
                'items.product',
            ])
            ->whereIn(
                'status',
                [
                    'approved',
                    'partially_received',
                ]
            )
            ->orderByDesc('po_date')
            ->orderByDesc('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PAYLOADS
        |--------------------------------------------------------------------------
        */

        $productPayloads = $products
            ->map(
                fn ($product) => $this->productPayload($product)
            )
            ->values();

        $orderPayloads = $orders
            ->map(
                fn ($order) => $this->orderPayload($order)
            )
            ->values();

        $purchaseOrderPayloads = $purchaseOrders
            ->map(function ($po) {
                return [
                    'id' => $po->id,

                    'po_number' => $po->po_number,

                    'status' => $po->status,

                    'status_label' => ucfirst(
                        str_replace('_', ' ', $po->status)
                    ),

                    'supplier_id' => $po->supplier_id,

                    'supplier_name' =>
                        $po->supplier->supplier_name ?? null,

                    'items' => $po->items
                        ->map(function ($item) {
                            $ordered = (int) $item->quantity;

                            $received = (int) (
                                $item->received_quantity ?? 0
                            );

                            return [
                                'product_id' => $item->product_id,

                                'product_name' =>
                                    $item->product->product_name
                                    ?? 'Unknown Product',

                                'brand' =>
                                    $item->product->brand
                                    ?? null,

                                'ordered_quantity' => $ordered,

                                'received_quantity' => $received,

                                'remaining_quantity' =>
                                    max(
                                        0,
                                        $ordered - $received
                                    ),

                                'unit_cost' =>
                                    $item->unit_cost !== null
                                        ? (float) $item->unit_cost
                                        : null,
                            ];
                        })
                        ->values(),
                ];
            })
            ->values();

        return view(
            'owner.sales-inventory',
            [
                'activeTab' =>
                    in_array(
                        $request->query('tab'),
                        [
                            'sale',
                            'orders',
                            'inventory',
                            'history',
                            'return',
                            'damage',
                        ],
                        true
                    )
                        ? $request->query('tab')
                        : 'sale',

                'products' => $products,

                'categories' => $categories,

                'suppliers' => $suppliers,

                'orderPayloads' => $orderPayloads,

                'movements' => $movements,

                'purchaseOrders' => $purchaseOrders,

                'productPayloads' => $productPayloads,

                'purchaseOrderPayloads' =>
                    $purchaseOrderPayloads,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ADD PRODUCT
    |--------------------------------------------------------------------------
    |
    | Creates:
    |
    | 1. Product
    | 2. Product <-> Supplier links
    | 3. Inventory row
    | 4. Opening InventoryBatch, if opening stock > 0
    | 5. Opening InventoryMovement, if opening stock > 0
    |
    | IMPORTANT:
    |
    | Opening stock uses movement_type = opening_balance.
    |
    | Actual supplier deliveries use movement_type = stock_in.
    |
    */

    public function productStore(Request $request)
    {
        $today = now()->toDateString();

        $validated = $request->validate(
            [
                'product_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'brand' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'category_id' => [
                    'required',
                    'integer',
                    'exists:categories,id',
                ],

                'api' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'base_oil' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'package_size' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'unit' => [
                    'required',
                    'string',
                    'max:255',
                ],

                /*
                |--------------------------------------------------------------------------
                | CUSTOMER SELLING PRICE
                |--------------------------------------------------------------------------
                */

                'unit_price' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'reorder_level' => [
                    'required',
                    'integer',
                    'min:0',
                ],

                'status' => [
                    'required',
                    Rule::in([
                        'active',
                        'inactive',
                    ]),
                ],

                /*
                |--------------------------------------------------------------------------
                | SUPPLIERS
                |--------------------------------------------------------------------------
                */

                'supplier_ids' => [
                    'nullable',
                    'array',
                ],

                'supplier_ids.*' => [
                    'integer',
                    'distinct',
                    Rule::exists(
                        'suppliers',
                        'id'
                    )->where(
                        'status',
                        'active'
                    ),
                ],

                /*
                |--------------------------------------------------------------------------
                | OPENING STOCK
                |--------------------------------------------------------------------------
                */

                'opening_stock' => [
                    'required',
                    'integer',
                    'min:0',
                ],

                /*
                |--------------------------------------------------------------------------
                | OPENING BATCH
                |--------------------------------------------------------------------------
                */

                'batch_number' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                /*
                |--------------------------------------------------------------------------
                | PURCHASE COST
                |--------------------------------------------------------------------------
                */

                'purchase_unit_cost' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'expiration_date' => [
                    'nullable',
                    'date',
                    'after_or_equal:' . $today,
                ],

                'best_before_date' => [
                    'nullable',
                    'date',
                    'after_or_equal:' . $today,
                ],
            ],
            [
                'supplier_ids.*.exists' =>
                    'One of the selected suppliers is not an active supplier.',

                'expiration_date.after_or_equal' =>
                    'The expiration date cannot be earlier than the opening stock receiving date.',

                'best_before_date.after_or_equal' =>
                    'The best before date cannot be earlier than the opening stock receiving date.',
            ]
        );

        $openingStock = (int) $validated['opening_stock'];

        $supplierIds = collect(
            $validated['supplier_ids'] ?? []
        )
            ->map(
                fn ($id) => (int) $id
            )
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | BRAND
        |--------------------------------------------------------------------------
        */

        $brand = trim(
            preg_replace(
                '/\s+/',
                ' ',
                (string) (
                    $validated['brand'] ?? ''
                )
            )
        );

        if ($brand === '') {
            $brand = null;
        } else {
            $existingBrand = Product::query()
                ->whereNotNull('brand')
                ->whereRaw(
                    'LOWER(brand) = ?',
                    [
                        mb_strtolower($brand),
                    ]
                )
                ->value('brand');

            if ($existingBrand) {
                $brand = $existingBrand;
            }
        }

        $product = DB::transaction(
            function () use (
                $validated,
                $openingStock,
                $supplierIds,
                $brand,
                $today
            ) {
                /*
                |--------------------------------------------------------------------------
                | 1. PRODUCT
                |--------------------------------------------------------------------------
                */

                $product = Product::create([
                    'product_name' =>
                        trim(
                            $validated['product_name']
                        ),

                    'brand' => $brand,

                    'category_id' =>
                        (int) $validated['category_id'],

                    'api' =>
                        $validated['api'] ?? null,

                    'base_oil' =>
                        $validated['base_oil'] ?? null,

                    'package_size' =>
                        $validated['package_size'] ?? null,

                    'unit' =>
                        trim($validated['unit']),

                    /*
                    | Customer selling price
                    */
                    'unit_price' =>
                        $validated['unit_price'],

                    'reorder_level' =>
                        (int) $validated['reorder_level'],

                    'status' =>
                        $validated['status'],
                ]);

                /*
                |--------------------------------------------------------------------------
                | 2. SUPPLIERS
                |--------------------------------------------------------------------------
                */

                $product->suppliers()->sync(
                    $supplierIds->all()
                );

                /*
                |--------------------------------------------------------------------------
                | 3. INVENTORY
                |--------------------------------------------------------------------------
                */

                Inventory::create([
                    'product_id' =>
                        $product->id,

                    'current_stock' =>
                        $openingStock,
                ]);

                /*
                |--------------------------------------------------------------------------
                | NO OPENING STOCK
                |--------------------------------------------------------------------------
                */

                if ($openingStock <= 0) {
                    return $product;
                }

                /*
                |--------------------------------------------------------------------------
                | OPENING STOCK SUPPLIER
                |--------------------------------------------------------------------------
                */

                $openingSupplier =
                    $supplierIds->count() === 1
                        ? Supplier::find(
                            $supplierIds->first()
                        )
                        : null;

                /*
                |--------------------------------------------------------------------------
                | BATCH NUMBER
                |--------------------------------------------------------------------------
                */

                $batchNumber = trim(
                    (string) (
                        $validated['batch_number']
                        ?? ''
                    )
                );

                if ($batchNumber === '') {
                    $batchNumber =
                        $this->generateBatchNumber(
                            $product->id,
                            $today
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | PURCHASE COST
                |--------------------------------------------------------------------------
                */

                $unitCost =
                    array_key_exists(
                        'purchase_unit_cost',
                        $validated
                    )
                    && $validated['purchase_unit_cost']
                        !== null
                    && $validated['purchase_unit_cost']
                        !== ''
                        ? (float) $validated[
                            'purchase_unit_cost'
                        ]
                        : null;

                /*
                |--------------------------------------------------------------------------
                | 4. OPENING INVENTORY BATCH
                |--------------------------------------------------------------------------
                */

                InventoryBatch::create([
                    'product_id' =>
                        $product->id,

                    'supplier_id' =>
                        $openingSupplier?->id,

                    'batch_number' =>
                        $batchNumber,

                    'received_date' =>
                        $today,

                    'expiration_date' =>
                        $validated['expiration_date']
                        ?? null,

                    'best_before_date' =>
                        $validated['best_before_date']
                        ?? null,

                    'quantity_received' =>
                        $openingStock,

                    'quantity_remaining' =>
                        $openingStock,

                    'unit_cost' =>
                        $unitCost,

                    'status' =>
                        'active',
                ]);

                /*
                |--------------------------------------------------------------------------
                | 5. OPENING BALANCE MOVEMENT
                |--------------------------------------------------------------------------
                |
                | IMPORTANT:
                |
                | This is NOT normal Stock In.
                |
                | It establishes the beginning balance.
                |
                */

                InventoryMovement::create([
                    'product_id' =>
                        $product->id,

                    'user_id' =>
                        auth()->id(),

                    'movement_type' =>
                        'opening_balance',

                    'transaction_date' =>
                        $today,

                    'quantity' =>
                        $openingStock,

                    'stock_before' =>
                        0,

                    'stock_after' =>
                        $openingStock,

                    'supplier_customer' =>
                        $openingSupplier?->supplier_name
                        ?? 'Opening balance',

                    'unit_cost' =>
                        $unitCost,

                    'unit_price' =>
                        $product->unit_price,

                    'amount' =>
                        $unitCost !== null
                            ? round(
                                $openingStock
                                * $unitCost,
                                2
                            )
                            : null,

                    'reason' =>
                        'Initial opening balance',

                    'customer_order_reference' =>
                        null,

                    'receipt_number' =>
                        null,

                    'received_by' =>
                        auth()->user()?->name,

                    'reference' =>
                        'Opening stock — batch '
                        . $batchNumber,
                ]);

                return $product;
            }
        );

        return $this->go(
            'inventory',
            'success',
            'Product added successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT INFORMATION
    |--------------------------------------------------------------------------
    */

    public function productInfo(Product $product)
    {
        $product->load([
            'category',
            'inventory',
            'suppliers',
            'inventoryBatches.supplier',
            'inventoryBatches.purchaseOrder',
        ]);

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER PURCHASE HISTORY
        |--------------------------------------------------------------------------
        */

        $suppliers = PurchaseOrder::with([
                'supplier',

                'items' => fn ($query) =>
                    $query->where(
                        'product_id',
                        $product->id
                    ),
            ])
            ->whereHas(
                'items',
                fn ($query) =>
                    $query->where(
                        'product_id',
                        $product->id
                    )
            )
            ->whereIn(
                'status',
                [
                    'approved',
                    'partially_received',
                    'received',
                ]
            )
            ->orderByDesc('po_date')
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(function ($po) {
                $item = $po->items->first();

                return [
                    'supplier' =>
                        $po->supplier->supplier_name
                        ?? '—',

                    'po_number' =>
                        $po->po_number,

                    'po_date' =>
                        $po->po_date
                            ? Carbon::parse(
                                $po->po_date
                            )->format('M d, Y')
                            : '—',

                    'unit_cost' =>
                        $item
                            ? (float) $item->unit_cost
                            : null,

                    'quantity' =>
                        $item
                            ? (int) $item->quantity
                            : 0,

                    'received' =>
                        $item
                            ? (int) (
                                $item->received_quantity
                                ?? 0
                            )
                            : 0,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | INVENTORY BATCHES
        |--------------------------------------------------------------------------
        */

        $batches = $product->inventoryBatches
            ->sortBy(function ($batch) {
                return
                    $batch->expiration_date?->timestamp
                    ?? $batch->best_before_date?->timestamp
                    ?? PHP_INT_MAX;
            })
            ->values()
            ->map(function ($batch) {
                return [
                    'id' =>
                        $batch->id,

                    'batch_number' =>
                        $batch->batch_number,

                    'supplier' =>
                        $batch->supplier?->supplier_name
                        ?? '—',

                    'purchase_order' =>
                        $batch->purchaseOrder?->po_number
                        ?? '—',

                    'received_date' =>
                        $batch->received_date
                            ? $batch->received_date->format(
                                'M d, Y'
                            )
                            : '—',

                    'expiration_date' =>
                        $batch->expiration_date
                            ? $batch->expiration_date->format(
                                'M d, Y'
                            )
                            : null,

                    'best_before_date' =>
                        $batch->best_before_date
                            ? $batch->best_before_date->format(
                                'M d, Y'
                            )
                            : null,

                    'quantity_received' =>
                        (int) $batch->quantity_received,

                    'quantity_remaining' =>
                        (int) $batch->quantity_remaining,

                    'unit_cost' =>
                        $batch->unit_cost !== null
                            ? (float) $batch->unit_cost
                            : null,

                    'status' =>
                        $batch->status,

                    'expiration_status' =>
                        $batch->expiration_status,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | MOVEMENT HISTORY
        |--------------------------------------------------------------------------
        */

        $movements = InventoryMovement::with('user')
            ->where(
                'product_id',
                $product->id
            )
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->map(function ($movement) {
                return [
                    'transaction_date' =>
                        $movement->transaction_date
                            ? $movement
                                ->transaction_date
                                ->format('M d, Y')
                            : '—',

                    'recorded_at' =>
                        $movement->created_at
                            ? $movement
                                ->created_at
                                ->format(
                                    'M d, Y h:i A'
                                )
                            : '—',

                    'type' =>
                        $movement->movement_type,

                    'quantity' =>
                        (int) $movement->quantity,

                    'before' =>
                        (int) $movement->stock_before,

                    'after' =>
                        (int) $movement->stock_after,

                    'reason' =>
                        $movement->reason,

                    'order' =>
                        $movement->customer_order_reference,

                    'receipt' =>
                        $movement->receipt_number,

                    'received_by' =>
                        $movement->received_by,

                    'reference' =>
                        $movement->reference,

                    'user' =>
                        $movement->user->name
                        ?? null,
                ];
            })
            ->values();

        return response()->json([
            'product' =>
                $this->productPayload($product),

            'suppliers' =>
                $suppliers,

            'batches' =>
                $batches,

            'movements' =>
                $movements,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CHECKOUT
    |--------------------------------------------------------------------------
    */

    public function checkout(Request $request)
    {
        $data = $this->validateCart(
            $request,
            true
        );

        try {
            $result = $this->sales->checkout(
                $data,
                auth()->id()
            );
        } catch (
            UniqueConstraintViolationException $e
        ) {
            if (
                CustomerOrder::where(
                    'checkout_token',
                    $data['checkout_token']
                )->exists()
            ) {
                return $this->go(
                    'orders',
                    'warning',
                    'This checkout was already submitted. No duplicate sale was created.'
                );
            }

            throw $e;
        }

        if ($result['duplicate']) {
            return $this->go(
                'orders',
                'warning',
                'This checkout was already submitted. No duplicate sale was created.'
            );
        }

        $message =
            "Sale completed. Order {$result['order']->order_number}";

        if ($result['payment']) {
            $message .=
                " | Receipt {$result['payment']->receipt_number}";
        }

        if ($result['change'] > 0) {
            $message .=
                ' | Change: ₱' .
                number_format(
                    $result['change'],
                    2
                );
        }

        $message .=
            '. Stock was deducted automatically.';

        return $this->go(
            'sale',
            'success',
            $message
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE OPEN ORDER
    |--------------------------------------------------------------------------
    */

    public function storeOrder(Request $request)
    {
        $data = $this->validateCart(
            $request,
            false
        );

        try {
            $result = $this->sales->saveOpenOrder(
                $data,
                auth()->id()
            );
        } catch (
            UniqueConstraintViolationException $e
        ) {
            if (
                CustomerOrder::where(
                    'checkout_token',
                    $data['checkout_token']
                )->exists()
            ) {
                return $this->go(
                    'orders',
                    'warning',
                    'This order was already submitted.'
                );
            }

            throw $e;
        }

        if ($result['duplicate']) {
            return $this->go(
                'orders',
                'warning',
                'This order was already submitted.'
            );
        }

        if ($result['reserved']) {
            return $this->go(
                'orders',
                'success',
                "Open order {$result['order']->order_number} saved. Stock is reserved; physical stock was NOT deducted."
            );
        }

        return $this->go(
            'orders',
            'warning',
            "Not enough stock for the whole order, so nothing was reserved. {$result['order']->order_number} was saved as For Purchasing. "
            . implode(
                ' ',
                $result['shortages']
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELEASE
    |--------------------------------------------------------------------------
    */

    public function release(
        Request $request,
        CustomerOrder $customerOrder
    ) {
        $validated = $request->validate([
            'transaction_date' => [
                'required',
                'date',
            ],

            'received_by' => [
                'required',
                'string',
                'max:255',
            ],

            'quantities' => [
                'nullable',
                'array',
            ],

            'quantities.*' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $order = $this->sales->release(
            $customerOrder,
            $validated['quantities'] ?? null,
            $validated['received_by'],
            $validated['transaction_date'],
            auth()->id()
        );

        return $this->go(
            'orders',
            'success',
            "Order {$order->order_number} released. Physical stock deducted; status: "
            . str_replace(
                '_',
                ' ',
                $order->status
            )
            . '.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT
    |--------------------------------------------------------------------------
    */

    public function payment(
        Request $request,
        CustomerOrder $customerOrder
    ) {
        $validated = $request->validate([
            'payment_date' => [
                'required',
                'date',
            ],

            'payment_method' => [
                'required',
                Rule::in(
                    SalesService::PAYMENT_METHODS
                ),
            ],

            'amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'receipt_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'reference_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'check_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'bank_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'check_date' => [
                'nullable',
                'date',
            ],

            'maturity_date' => [
                'nullable',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $result = $this->sales->recordPayment(
            $customerOrder,
            $validated,
            auth()->id()
        );

        $message =
            "Payment recorded. Receipt {$result['payment']->receipt_number}. Inventory was not changed.";

        if ($result['change'] > 0) {
            $message .=
                ' Change: ₱' .
                number_format(
                    $result['change'],
                    2
                );
        }

        return $this->go(
            'orders',
            'success',
            $message
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RECHECK
    |--------------------------------------------------------------------------
    */

    public function recheck(
        CustomerOrder $customerOrder
    ) {
        $result = $this->sales->recheck(
            $customerOrder,
            auth()->id()
        );

        if ($result['available']) {
            return $this->go(
                'orders',
                'success',
                "{$result['order']->order_number}: stock is available. You can now confirm the order."
            );
        }

        return $this->go(
            'orders',
            'warning',
            "{$result['order']->order_number} is For Purchasing. "
            . implode(
                ' ',
                $result['shortages']
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIRM
    |--------------------------------------------------------------------------
    */

    public function confirm(
        CustomerOrder $customerOrder
    ) {
        $order = $this->sales->confirm(
            $customerOrder,
            auth()->id()
        );

        return $this->go(
            'orders',
            'success',
            "Order {$order->order_number} confirmed. Stock reserved; physical stock not deducted."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */

    public function cancel(
        CustomerOrder $customerOrder
    ) {
        $order = $this->sales->cancel(
            $customerOrder
        );

        return $this->go(
            'orders',
            'success',
            "Order {$order->order_number} cancelled. Reservations released."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VOID
    |--------------------------------------------------------------------------
    */

    public function voidOrder(
        Request $request,
        CustomerOrder $customerOrder
    ) {
        $validated = $request->validate([
            'void_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $order = $this->sales->void(
            $customerOrder,
            $validated['void_reason'],
            auth()->id()
        );

        return $this->go(
            'orders',
            'success',
            "Order {$order->order_number} voided. Stock restored and payments voided."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STOCK IN
    |--------------------------------------------------------------------------
    |
    | Stock In means ACTUAL goods received from the supplier.
    |
    | Purchase Order creation / approval does NOT increase stock.
    |
    | This method:
    |
    | 1. Increases inventory
    | 2. Creates inventory batch
    | 3. Updates PO received quantity
    | 4. Updates PO status
    | 5. Creates stock_in movement
    |
    */

    public function stockIn(Request $request)
    {
        $validated = $request->validate(
            [
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

                'batch_number' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'expiration_date' => [
                    'nullable',
                    'date',
                    'after_or_equal:transaction_date',
                ],

                'best_before_date' => [
                    'nullable',
                    'date',
                    'after_or_equal:transaction_date',
                ],

                'reason' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
            ],
            [
                'expiration_date.after_or_equal' =>
                    'The expiration date cannot be earlier than the transaction date.',

                'best_before_date.after_or_equal' =>
                    'The best before date cannot be earlier than the transaction date.',
            ]
        );

        DB::transaction(
            function () use ($validated) {

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
                | APPROVED / PARTIALLY RECEIVED ONLY
                |--------------------------------------------------------------------------
                */

                if (
                    ! in_array(
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
                |--------------------------------------------------------------------------
                | PRODUCT MUST BELONG TO PO
                |--------------------------------------------------------------------------
                */

                $poItem = $purchaseOrder->items
                    ->firstWhere(
                        'product_id',
                        (int) $validated['product_id']
                    );

                if (! $poItem) {
                    throw ValidationException::withMessages([
                        'product_id' =>
                            'The selected product is not included in this Purchase Order.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | REMAINING PO QUANTITY
                |--------------------------------------------------------------------------
                */

                $received = (int) (
                    $poItem->received_quantity
                );

                $remaining =
                    (int) $poItem->quantity
                    - $received;

                $quantity =
                    (int) $validated['quantity'];

                if ($remaining <= 0) {
                    throw ValidationException::withMessages([
                        'quantity' =>
                            'This Purchase Order item has already been fully received.',
                    ]);
                }

                if ($quantity > $remaining) {
                    throw ValidationException::withMessages([
                        'quantity' =>
                            "Only {$remaining} unit(s) remain to be received for this Purchase Order item.",
                    ]);
                }

                $product = Product::findOrFail(
                    $validated['product_id']
                );

                /*
                |--------------------------------------------------------------------------
                | BATCH NUMBER
                |--------------------------------------------------------------------------
                */

                $batchNumber = trim(
                    (string) (
                        $validated['batch_number']
                        ?? ''
                    )
                );

                if ($batchNumber === '') {
                    $batchNumber =
                        $this->generateBatchNumber(
                            $product->id,
                            $validated['transaction_date']
                        );
                } else {
                    $duplicate =
                        InventoryBatch::query()
                            ->where(
                                'product_id',
                                $product->id
                            )
                            ->where(
                                'batch_number',
                                $batchNumber
                            )
                            ->lockForUpdate()
                            ->exists();

                    if ($duplicate) {
                        throw ValidationException::withMessages([
                            'batch_number' =>
                                "Batch number {$batchNumber} already exists for this product.",
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | LOCK INVENTORY
                |--------------------------------------------------------------------------
                */

                $inventory = $this->sales
                    ->lockInventories([
                        $product->id,
                    ])
                    ->first();

                $before =
                    (int) $inventory->current_stock;

                $after =
                    $before + $quantity;

                $inventory->update([
                    'current_stock' =>
                        $after,
                ]);

                /*
                |--------------------------------------------------------------------------
                | PURCHASE COST
                |--------------------------------------------------------------------------
                */

                $unitCost =
                    $poItem->unit_cost;

                $amount =
                    $unitCost !== null
                        ? $quantity * $unitCost
                        : null;

                /*
                |--------------------------------------------------------------------------
                | UPDATE PO ITEM
                |--------------------------------------------------------------------------
                */

                $poItem->update([
                    'received_quantity' =>
                        $received + $quantity,
                ]);

                /*
                |--------------------------------------------------------------------------
                | UPDATE PO STATUS
                |--------------------------------------------------------------------------
                */

                $allReceived =
                    $purchaseOrder
                        ->items()
                        ->whereColumn(
                            'received_quantity',
                            '<',
                            'quantity'
                        )
                        ->doesntExist();

                $purchaseOrder->update([
                    'status' =>
                        $allReceived
                            ? 'received'
                            : 'partially_received',
                ]);

                /*
                |--------------------------------------------------------------------------
                | INVENTORY BATCH
                |--------------------------------------------------------------------------
                */

                InventoryBatch::create([
                    'product_id' =>
                        $product->id,

                    'supplier_id' =>
                        $purchaseOrder->supplier_id,

                    'purchase_order_id' =>
                        $purchaseOrder->id,

                    'batch_number' =>
                        $batchNumber,

                    'received_date' =>
                        $validated['transaction_date'],

                    'expiration_date' =>
                        $validated['expiration_date']
                        ?? null,

                    'best_before_date' =>
                        $validated['best_before_date']
                        ?? null,

                    'quantity_received' =>
                        $quantity,

                    'quantity_remaining' =>
                        $quantity,

                    'unit_cost' =>
                        $unitCost,

                    'status' =>
                        'active',
                ]);

                /*
                |--------------------------------------------------------------------------
                | STOCK IN MOVEMENT
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
                        $quantity,

                    'stock_before' =>
                        $before,

                    'stock_after' =>
                        $after,

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
                        auth()->user()?->name,

                    'reference' =>
                        $purchaseOrder->po_number,
                ]);
            }
        );

        return $this->go(
            'inventory',
            'success',
            'Supplier delivery recorded successfully. Inventory, batch information, and Purchase Order status were updated.'
        );
    }
    /*
    |--------------------------------------------------------------------------
    | RETURN STOCK
    |--------------------------------------------------------------------------
    |
    | A Return means usable inventory comes back into DPAM.
    |
    | Example:
    |
    | Current stock = 100
    | Customer returns = 2
    | New stock = 102
    |
    | This does NOT represent a supplier delivery.
    | Therefore the movement type is:
    |
    | return
    |
    */

    public function returnStock(Request $request)
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

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'customer_order_reference' => [
                'nullable',
                'string',
                'max:100',
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

            'received_by' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | LOCK INVENTORY
            |--------------------------------------------------------------------------
            */

            $inventory = $this->sales
                ->lockInventories([
                    (int) $validated['product_id'],
                ])
                ->first();

            if (! $inventory) {
                throw ValidationException::withMessages([
                    'product_id' =>
                        'Inventory record for the selected product was not found.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | PRODUCT
            |--------------------------------------------------------------------------
            */

            $product = Product::findOrFail(
                $validated['product_id']
            );

            /*
            |--------------------------------------------------------------------------
            | STOCK BEFORE
            |--------------------------------------------------------------------------
            */

            $before = (int) $inventory->current_stock;

            /*
            |--------------------------------------------------------------------------
            | RETURN QUANTITY
            |--------------------------------------------------------------------------
            */

            $quantity = (int) $validated['quantity'];

            /*
            |--------------------------------------------------------------------------
            | STOCK AFTER
            |--------------------------------------------------------------------------
            */

            $after = $before + $quantity;

            /*
            |--------------------------------------------------------------------------
            | UPDATE INVENTORY
            |--------------------------------------------------------------------------
            */

            $inventory->update([
                'current_stock' => $after,
            ]);

            /*
            |--------------------------------------------------------------------------
            | INVENTORY MOVEMENT
            |--------------------------------------------------------------------------
            |
            | Return increases inventory.
            |
            */

            InventoryMovement::create([
                'product_id' =>
                    $product->id,

                'user_id' =>
                    auth()->id(),

                'movement_type' =>
                    'return',

                'transaction_date' =>
                    $validated['transaction_date'],

                'quantity' =>
                    $quantity,

                'stock_before' =>
                    $before,

                'stock_after' =>
                    $after,

                'supplier_customer' =>
                    'Customer Return',

                'unit_cost' =>
                    null,

                'unit_price' =>
                    $product->unit_price,

                'amount' =>
                    round(
                        $quantity
                        * (float) $product->unit_price,
                        2
                    ),

                'reason' =>
                    $validated['reason'],

                'customer_order_reference' =>
                    $validated['customer_order_reference']
                    ?? null,

                'receipt_number' =>
                    null,

                'received_by' =>
                    $validated['received_by'],

                'reference' =>
                    $validated['reference']
                    ?? null,
            ]);
        });

        return $this->go(
            'inventory',
            'success',
            'Customer return recorded successfully. The returned quantity was added back to inventory.'
        );
    }

    
    /*
    |--------------------------------------------------------------------------
    | RECORD DAMAGED STOCK
    |--------------------------------------------------------------------------
    |
    | Damage means inventory is no longer usable.
    |
    | Example:
    |
    | Current stock = 100
    | Damaged = 3
    | New stock = 97
    |
    | Damage therefore decreases physical inventory.
    |
    */

    public function damageStock(Request $request)
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

            'quantity' => [
                'required',
                'integer',
                'min:1',
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

            'received_by' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | LOCK INVENTORY
            |--------------------------------------------------------------------------
            */

            $inventory = $this->sales
                ->lockInventories([
                    (int) $validated['product_id'],
                ])
                ->first();

            if (! $inventory) {
                throw ValidationException::withMessages([
                    'product_id' =>
                        'Inventory record for the selected product was not found.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | PRODUCT
            |--------------------------------------------------------------------------
            */

            $product = Product::findOrFail(
                $validated['product_id']
            );

            /*
            |--------------------------------------------------------------------------
            | STOCK BEFORE
            |--------------------------------------------------------------------------
            */

            $before = (int) $inventory->current_stock;

            /*
            |--------------------------------------------------------------------------
            | DAMAGE QUANTITY
            |--------------------------------------------------------------------------
            */

            $quantity = (int) $validated['quantity'];

            /*
            |--------------------------------------------------------------------------
            | RESERVED STOCK PROTECTION
            |--------------------------------------------------------------------------
            |
            | Damaged stock cannot include units that are already
            | reserved for open customer orders.
            |
            */

            $reserved = (int) CustomerOrderItem::query()
                ->where(
                    'product_id',
                    $product->id
                )
                ->whereHas(
                    'customerOrder',
                    function ($query) {
                        $query->whereIn(
                            'status',
                            SalesService::OPEN_STATUSES
                        );
                    }
                )
                ->sum('reserved_quantity');

            /*
            |--------------------------------------------------------------------------
            | VALIDATE AVAILABLE STOCK
            |--------------------------------------------------------------------------
            */

            $availableForDamage =
                max(
                    0,
                    $before - $reserved
                );

            if ($quantity > $availableForDamage) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        "Only {$availableForDamage} unit(s) can be recorded as damaged because {$reserved} unit(s) are reserved for open customer orders.",
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | STOCK AFTER
            |--------------------------------------------------------------------------
            */

            $after = $before - $quantity;

            /*
            |--------------------------------------------------------------------------
            | UPDATE INVENTORY
            |--------------------------------------------------------------------------
            */

            $inventory->update([
                'current_stock' => $after,
            ]);

            /*
            |--------------------------------------------------------------------------
            | INVENTORY MOVEMENT
            |--------------------------------------------------------------------------
            */

            InventoryMovement::create([
                'product_id' =>
                    $product->id,

                'user_id' =>
                    auth()->id(),

                'movement_type' =>
                    'damaged',

                'transaction_date' =>
                    $validated['transaction_date'],

                'quantity' =>
                    $quantity,

                'stock_before' =>
                    $before,

                'stock_after' =>
                    $after,

                'supplier_customer' =>
                    null,

                'unit_cost' =>
                    null,

                'unit_price' =>
                    $product->unit_price,

                'amount' =>
                    round(
                        $quantity
                        * (float) $product->unit_price,
                        2
                    ),

                'reason' =>
                    $validated['reason'],

                'customer_order_reference' =>
                    null,

                'receipt_number' =>
                    null,

                'received_by' =>
                    $validated['received_by'],

                'reference' =>
                    $validated['reference']
                    ?? null,
            ]);
        });

        return $this->go(
            'inventory',
            'success',
            'Damaged stock recorded successfully. The damaged quantity was removed from inventory.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STOCK ADJUSTMENT
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

        $changed = DB::transaction(
            function () use ($validated) {

                $inventory = $this->sales
                    ->lockInventories([
                        $validated['product_id'],
                    ])
                    ->first();

                $before =
                    (int) $inventory->current_stock;

                $after =
                    (int) $validated['actual_stock'];

                /*
                |--------------------------------------------------------------------------
                | RESERVED STOCK PROTECTION
                |--------------------------------------------------------------------------
                */

                $reserved =
                    (int) CustomerOrderItem::query()
                        ->where(
                            'product_id',
                            $validated['product_id']
                        )
                        ->whereHas(
                            'customerOrder',
                            function ($query) {
                                $query->whereIn(
                                    'status',
                                    SalesService::OPEN_STATUSES
                                );
                            }
                        )
                        ->sum(
                            'reserved_quantity'
                        );

                if ($after < $reserved) {
                    throw ValidationException::withMessages([
                        'actual_stock' =>
                            "The physical stock cannot be adjusted to {$after} because {$reserved} unit(s) are already reserved for open customer orders.",
                    ]);
                }

                $difference =
                    $after - $before;

                if ($difference === 0) {
                    return false;
                }

                $inventory->update([
                    'current_stock' =>
                        $after,
                ]);

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
                        $before,

                    'stock_after' =>
                        $after,

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

                return true;
            }
        );

        return $changed
            ? $this->go(
                'inventory',
                'success',
                'Stock adjustment recorded successfully.'
            )
            : $this->go(
                'inventory',
                'warning',
                'The physical count equals the system stock. No adjustment was needed.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | REDIRECT HELPER
    |--------------------------------------------------------------------------
    */

    private function go(
        string $tab,
        string $type,
        string $message
    ) {
        return redirect()
            ->route(
                'owner.sales-inventory',
                [
                    'tab' => $tab,
                ]
            )
            ->with(
                $type,
                $message
            );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE SYSTEM BATCH NUMBER
    |--------------------------------------------------------------------------
    */

    private function generateBatchNumber(
        int $productId,
        string $date
    ): string {
        $day =
            Carbon::parse($date)
                ->format('Ymd');

        do {
            $candidate =
                "SYS-{$day}-P{$productId}-"
                . strtoupper(
                    Str::random(4)
                );

            $exists =
                InventoryBatch::query()
                    ->where(
                        'product_id',
                        $productId
                    )
                    ->where(
                        'batch_number',
                        $candidate
                    )
                    ->exists();
        } while ($exists);

        return $candidate;
    }

    /*
    |--------------------------------------------------------------------------
    | CART VALIDATION
    |--------------------------------------------------------------------------
    */

    private function validateCart(
        Request $request,
        bool $checkout
    ): array {
        $rules = [
            'customer_name' => [
                'nullable',
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

            'checkout_token' => [
                'required',
                'string',
                'max:100',
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
        ];

        if ($checkout) {
            $rules += [
                'payment_method' => [
                    'required',
                    Rule::in(
                        SalesService::PAYMENT_METHODS
                    ),
                ],

                'amount_paid' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'receipt_number' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'received_by' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'reference_number' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'check_number' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'bank_name' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'check_date' => [
                    'nullable',
                    'date',
                ],

                'maturity_date' => [
                    'nullable',
                    'date',
                ],
            ];
        }

        return $request->validate(
            $rules
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT PAYLOAD
    |--------------------------------------------------------------------------
    */

    private function productPayload(
        Product $p
    ): array {
        $batches =
            $p->relationLoaded(
                'inventoryBatches'
            )
                ? $p->inventoryBatches
                : collect();

        /*
        |--------------------------------------------------------------------------
        | ALL MOVEMENTS FOR THIS PRODUCT
        |--------------------------------------------------------------------------
        |
        | Ordered from oldest to newest because stock-card balances
        | follow the movement sequence.
        |
        */

        $movementsForProduct =
            $p->inventoryMovements()
                ->orderBy('transaction_date')
                ->orderBy('id')
                ->get();

        /*
        |--------------------------------------------------------------------------
        | BEGINNING BALANCE
        |--------------------------------------------------------------------------
        |
        | Opening balance establishes the initial beginning balance.
        |
        | Example:
        |
        | Opening balance = 100
        |
        | Beginning Balance = 100
        | Stock In         = 0
        | Stock Out        = 0
        | Remaining        = 100
        |
        */

        $openingMovement =
            $movementsForProduct
                ->firstWhere(
                    'movement_type',
                    'opening_balance'
                );

        $beginningBalance =
            $openingMovement
                ? (int) $openingMovement->stock_after
                : 0;

        /*
        |--------------------------------------------------------------------------
        | NORMAL STOCK IN
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Opening balance is NOT included here.
        |
        | Only actual supplier deliveries are Stock In.
        |
        */

        $stockIn =
            (int) $movementsForProduct
                ->filter(function ($movement) {
                    return $movement->movement_type
                        === 'stock_in';
                })
                ->sum(function ($movement) {
                    return abs(
                        (int) $movement->quantity
                    );
                });

        /*
        |--------------------------------------------------------------------------
        | STOCK OUT
        |--------------------------------------------------------------------------
        */

        $stockOut =
            (int) $movementsForProduct
                ->filter(function ($movement) {
                    return $movement->movement_type
                        === 'stock_out';
                })
                ->sum(function ($movement) {
                    return abs(
                        (int) $movement->quantity
                    );
                });

        /*
        |--------------------------------------------------------------------------
        | RETURNS
        |--------------------------------------------------------------------------
        */

        $returns =
            (int) $movementsForProduct
                ->filter(function ($movement) {
                    return $movement->movement_type
                        === 'return';
                })
                ->sum(function ($movement) {
                    return abs(
                        (int) $movement->quantity
                    );
                });

        /*
        |--------------------------------------------------------------------------
        | DAMAGED
        |--------------------------------------------------------------------------
        */

        $damaged =
            (int) $movementsForProduct
                ->filter(function ($movement) {
                    return $movement->movement_type
                        === 'damaged';
                })
                ->sum(function ($movement) {
                    return abs(
                        (int) $movement->quantity
                    );
                });

        /*
        |--------------------------------------------------------------------------
        | EXPIRATION
        |--------------------------------------------------------------------------
        */

        $activeBatches =
            $batches
                ->filter(
                    fn ($batch) =>
                        (int) $batch->quantity_remaining > 0
                )
                ->sortBy(function ($batch) {
                    return
                        $batch->expiration_date?->timestamp
                        ?? $batch->best_before_date?->timestamp
                        ?? PHP_INT_MAX;
                })
                ->values();

        $earliestBatch =
            $activeBatches->first();

        /*
        |--------------------------------------------------------------------------
        | SUPPLIERS
        |--------------------------------------------------------------------------
        */

        $suppliers =
            $p->relationLoaded('suppliers')
                ? $p->suppliers
                    ->map(
                        fn ($supplier) => [
                            'id' =>
                                $supplier->id,

                            'name' =>
                                $supplier->supplier_name,

                            'status' =>
                                $supplier->status,
                        ]
                    )
                    ->values()
                : collect();

        /*
        |--------------------------------------------------------------------------
        | PRODUCT PAYLOAD
        |--------------------------------------------------------------------------
        */

        return [
            'id' =>
                $p->id,

            'name' =>
                $p->product_name,

            'brand' =>
                $p->brand,

            'category' =>
                $p->category->category_name
                ?? null,

            'api' =>
                $p->api,

            'base_oil' =>
                $p->base_oil,

            'package_size' =>
                $p->package_size,

            'unit' =>
                $p->unit,

            /*
            | Customer selling price
            */
            'price' =>
                (float) $p->unit_price,

            /*
            |--------------------------------------------------------------------------
            | STOCK
            |--------------------------------------------------------------------------
            */

            'current_stock' =>
                (int) (
                    $p->inventory->current_stock
                    ?? 0
                ),

            'available_stock' =>
                (int) $p->available_stock,

            'reserved_stock' =>
                (int) (
                    ($p->inventory->current_stock ?? 0)
                    - $p->available_stock
                ),

            /*
            |--------------------------------------------------------------------------
            | STOCK CARD SUMMARY
            |--------------------------------------------------------------------------
            */

            'beginning_balance' =>
                $beginningBalance,

            'stock_in' =>
                $stockIn,

            'stock_out' =>
                $stockOut,

            'returns' =>
                $returns,

            'damaged' =>
                $damaged,

            'remaining_balance' =>
                (int) (
                    $p->inventory->current_stock
                    ?? 0
                ),

            /*
            |--------------------------------------------------------------------------
            | PRODUCT STATUS
            |--------------------------------------------------------------------------
            */

            'reorder_level' =>
                (int) $p->reorder_level,

            'status' =>
                $p->status,

            'stock_status' =>
                $p->stock_status,

            /*
            |--------------------------------------------------------------------------
            | SUPPLIERS
            |--------------------------------------------------------------------------
            */

            'suppliers' =>
                $suppliers,

            'supplier_names' =>
                $suppliers
                    ->pluck('name')
                    ->values(),

            /*
            |--------------------------------------------------------------------------
            | EXPIRATION
            |--------------------------------------------------------------------------
            */

            'earliest_expiration' =>
                $earliestBatch?->expiration_date
                    ? $earliestBatch
                        ->expiration_date
                        ->format('M d, Y')
                    : null,

            'earliest_best_before' =>
                $earliestBatch?->best_before_date
                    ? $earliestBatch
                        ->best_before_date
                        ->format('M d, Y')
                    : null,

            'earliest_expiration_status' =>
                $earliestBatch
                    ? $earliestBatch->expiration_status
                    : 'NO EXPIRATION',

            /*
            |--------------------------------------------------------------------------
            | BATCHES
            |--------------------------------------------------------------------------
            */

            'batches' =>
                $activeBatches
                    ->map(function ($batch) {
                        return [
                            'id' =>
                                $batch->id,

                            'batch_number' =>
                                $batch->batch_number,

                            'supplier' =>
                                $batch->supplier?->supplier_name
                                ?? '—',

                            'received_date' =>
                                $batch->received_date
                                    ? $batch
                                        ->received_date
                                        ->format(
                                            'M d, Y'
                                        )
                                    : '—',

                            'expiration_date' =>
                                $batch->expiration_date
                                    ? $batch
                                        ->expiration_date
                                        ->format(
                                            'M d, Y'
                                        )
                                    : null,

                            'best_before_date' =>
                                $batch->best_before_date
                                    ? $batch
                                        ->best_before_date
                                        ->format(
                                            'M d, Y'
                                        )
                                    : null,

                            'quantity_received' =>
                                (int) $batch
                                    ->quantity_received,

                            'quantity_remaining' =>
                                (int) $batch
                                    ->quantity_remaining,

                            'unit_cost' =>
                                $batch->unit_cost !== null
                                    ? (float) $batch->unit_cost
                                    : null,

                            'status' =>
                                $batch->status,
                        ];
                    })
                    ->values(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ORDER PAYLOAD
    |--------------------------------------------------------------------------
    */

    private function orderPayload(
        CustomerOrder $o
    ): array {
        $status = $o->status;

        $fulfilledQty =
            (int) $o->items
                ->sum('fulfilled_quantity');

        $livePayments =
            $o->payments
                ->where(
                    'status',
                    '!=',
                    'voided'
                )
                ->count();

        $remainingQty =
            $o->remaining_quantity;

        $labels = [
            'pending_inventory_check' =>
                'Pending Inventory Check',

            'confirmed' =>
                'Confirmed (Open)',

            'partially_fulfilled' =>
                'Partially Fulfilled',

            'fulfilled' =>
                'Fulfilled',

            'for_purchasing' =>
                'For Purchasing',

            'cancelled' =>
                $o->voided_at
                    ? 'Voided'
                    : 'Cancelled',
        ];

        return [
            'id' =>
                $o->id,

            'order_number' =>
                $o->order_number,

            'customer_name' =>
                $o->customer_name,

            'customer_contact' =>
                $o->customer_contact,

            'order_date' =>
                $o->order_date
                    ? $o->order_date->format(
                        'M d, Y'
                    )
                    : '—',

            'status' =>
                $status,

            'status_label' =>
                $labels[$status]
                ?? ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        $status
                    )
                ),

            'inventory_check_status' =>
                $o->inventory_check_status,

            'inventory_check_notes' =>
                $o->inventory_check_notes,

            'checked_by' =>
                $o->inventoryCheckedBy->name
                ?? null,

            'notes' =>
                $o->notes,

            'voided_at' =>
                $o->voided_at
                    ? $o->voided_at->format(
                        'M d, Y h:i A'
                    )
                    : null,

            'voided_by' =>
                $o->voidedBy->name
                ?? null,

            'void_reason' =>
                $o->void_reason,

            'total' =>
                $o->total_amount,

            'collected' =>
                $o->collected_amount,

            'pending' =>
                $o->pending_amount,

            'balance' =>
                $o->balance_amount,

            'payable' =>
                $o->payable_amount,

            'payment_status' =>
                $o->payment_status,

            'remaining_qty' =>
                $remainingQty,

            'can_recheck' =>
                in_array(
                    $status,
                    [
                        'pending_inventory_check',
                        'for_purchasing',
                    ],
                    true
                ),

            'can_confirm' =>
                in_array(
                    $status,
                    [
                        'pending_inventory_check',
                        'for_purchasing',
                    ],
                    true
                ),

            'can_cancel' =>
                $status !== 'cancelled'
                && $fulfilledQty === 0
                && $livePayments === 0,

            'can_release' =>
                in_array(
                    $status,
                    SalesService::OPEN_STATUSES,
                    true
                )
                && $remainingQty > 0,

            'can_pay' =>
                in_array(
                    $status,
                    [
                        'confirmed',
                        'partially_fulfilled',
                        'fulfilled',
                    ],
                    true
                )
                && $o->payable_amount > 0.004,

            'can_void' =>
                $status !== 'cancelled'
                && ! $o->voided_at,

            /*
            |--------------------------------------------------------------------------
            | ORDER ITEMS
            |--------------------------------------------------------------------------
            */

            'items' =>
                $o->items
                    ->map(function ($i) {
                        return [
                            'id' =>
                                $i->id,

                            'product_name' =>
                                $i->product->product_name
                                ?? '—',

                            'quantity' =>
                                (int) $i->quantity,

                            'reserved' =>
                                (int) $i->reserved_quantity,

                            'fulfilled' =>
                                (int) $i->fulfilled_quantity,

                            'remaining' =>
                                max(
                                    0,
                                    (int) $i->quantity
                                    - (int) $i->fulfilled_quantity
                                ),

                            'unit_price' =>
                                (float) $i->unit_price,

                            'subtotal' =>
                                (float) $i->subtotal,
                        ];
                    })
                    ->values(),

            /*
            |--------------------------------------------------------------------------
            | PAYMENTS
            |--------------------------------------------------------------------------
            */

            'payments' =>
                $o->payments
                    ->sortBy('id')
                    ->map(function ($p) {
                        return [
                            'receipt' =>
                                $p->receipt_number,

                            'method' =>
                                $p->payment_method,

                            'status' =>
                                $p->status,

                            'amount' =>
                                (float) $p->amount,

                            'date' =>
                                $p->payment_date
                                    ? $p->payment_date->format(
                                        'M d, Y'
                                    )
                                    : '—',
                        ];
                    })
                    ->values(),
        ];
    }
}