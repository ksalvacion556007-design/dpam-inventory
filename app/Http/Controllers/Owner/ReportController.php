<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'date_from'  => ['nullable', 'date'],
            'date_to'    => ['nullable', 'date', 'after_or_equal:date_from'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
        ]);

        $dateFrom = $validated['date_from'] ?? null;
        $dateTo = $validated['date_to'] ?? null;

        $productId = isset($validated['product_id'])
            ? (int) $validated['product_id']
            : null;

        /*
        |--------------------------------------------------------------------------
        | INVENTORY STATUS
        |--------------------------------------------------------------------------
        */

        $activeProducts = Product::with([
                'category',
                'inventory',
            ])
            ->where('status', 'active')
            ->orderBy('product_name')
            ->get();

        $inventory = $activeProducts->map(function ($product) {

            $stock = (int) ($product->inventory->current_stock ?? 0);
            $reorder = (int) $product->reorder_level;
            $price = (float) $product->unit_price;

            return [
                'product_id'    => $product->id,
                'product_name'  => $product->product_name,
                'brand'         => $product->brand,
                'category'      => $product->category->category_name ?? null,
                'unit'          => $product->unit,
                'current_stock' => $stock,
                'reorder_level' => $reorder,
                'unit_price'    => $price,
                'amount'        => $stock * $price,

                'status' => $stock <= 0
                    ? 'Out of Stock'
                    : ($stock <= $reorder
                        ? 'Low Stock'
                        : 'In Stock'),
            ];
        })->values();

        $totalProducts = $inventory->count();

        $totalInventoryQuantity = $inventory->sum('current_stock');

        $totalInventoryValue = $inventory->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | STOCK CARD
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Stock Card is product-specific.
        |
        | If no product is selected, no stock-card rows are generated.
        |
        | The complete history of the selected product is replayed first.
        | Only after the running balance is calculated do we apply the
        | selected date range.
        |
        */

        $stockCard = collect();

        if ($productId) {

            $movements = InventoryMovement::with([
                    'product',
                    'user',
                ])
                ->where('product_id', $productId)
                ->orderBy('transaction_date')
                ->orderBy('id')
                ->get();

            $running = 0;

            foreach ($movements as $movement) {

                $date = $movement->transaction_date
                    ?: $movement->created_at;

                $dateKey = $date
                    ? $date->format('Y-m-d')
                    : null;

                $type = (string) $movement->movement_type;

                $quantity = (int) $movement->quantity;

                /*
                |--------------------------------------------------------------------------
                | OPENING BALANCE
                |--------------------------------------------------------------------------
                |
                | Opening Stock establishes the initial balance.
                |
                | It is NOT counted as Stock In.
                |
                | Example:
                |
                | Opening Stock = 100
                |
                | Date | Beginning | In | Out | Remaining
                |      |     100   | 0  |  0  |    100
                |
                */

                $isOpening =
                    $type === 'opening_balance'
                    ||
                    (
                        $type === 'stock_in'
                        && str_contains(
                            strtolower((string) $movement->reason),
                            'opening'
                        )
                    );

                if ($isOpening) {

                    /*
                    | Prefer stock_after because it represents the actual
                    | resulting inventory balance.
                    */
                    $openingBalance = (int) $movement->stock_after;

                    /*
                    | Fallback for older records where stock_after may be 0.
                    */
                    if ($openingBalance === 0 && $quantity !== 0) {
                        $openingBalance = abs($quantity);
                    }

                    $running = max(0, $openingBalance);

                    $stockCard->push([
                        'm' => $movement,

                        'date' => $date,

                        'date_key' => $dateKey,

                        'product_id' => (int) $movement->product_id,

                        'product_name' =>
                            $movement->product->product_name ?? '—',

                        'brand' =>
                            $movement->product->brand ?? null,

                        'unit' =>
                            $movement->product->unit ?? null,

                        'begin' => $running,

                        'in' => 0,

                        'out' => 0,

                        'remain' => $running,
                    ]);

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | NORMAL MOVEMENT
                |--------------------------------------------------------------------------
                */

                $begin = $running;

                $stockIn = 0;

                $stockOut = 0;

                /*
                |--------------------------------------------------------------------------
                | SUPPLIER DELIVERY
                |--------------------------------------------------------------------------
                */

                if ($type === 'stock_in') {

                    $stockIn = abs($quantity);

                    $running += $stockIn;
                }

                /*
                |--------------------------------------------------------------------------
                | CUSTOMER RELEASE / SALE
                |--------------------------------------------------------------------------
                */

                elseif ($type === 'stock_out') {

                    $stockOut = abs($quantity);

                    $running -= $stockOut;
                }

                /*
                |--------------------------------------------------------------------------
                | RETURNS
                |--------------------------------------------------------------------------
                |
                | A restockable return increases usable inventory.
                | For the six-column Stock Card it is treated as Stock In.
                |
                */

                elseif ($type === 'return') {

                    $stockIn = abs($quantity);

                    $running += $stockIn;
                }

                /*
                |--------------------------------------------------------------------------
                | DAMAGED
                |--------------------------------------------------------------------------
                |
                | Damaged items leave usable inventory.
                | For the six-column Stock Card it is treated as Stock Out.
                |
                */

                elseif ($type === 'damaged') {

                    $stockOut = abs($quantity);

                    $running -= $stockOut;
                }

                /*
                |--------------------------------------------------------------------------
                | ADJUSTMENTS / REVERSALS
                |--------------------------------------------------------------------------
                |
                | These retain their signed quantity.
                |
                */

                else {

                    $running += $quantity;
                }

                /*
                | Prevent negative displayed inventory.
                */
                $running = max(0, $running);

                $stockCard->push([
                    'm' => $movement,

                    'date' => $date,

                    'date_key' => $dateKey,

                    'product_id' => (int) $movement->product_id,

                    'product_name' =>
                        $movement->product->product_name ?? '—',

                    'brand' =>
                        $movement->product->brand ?? null,

                    'unit' =>
                        $movement->product->unit ?? null,

                    'begin' => $begin,

                    'in' => $stockIn,

                    'out' => $stockOut,

                    'remain' => $running,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | APPLY DATE FILTER AFTER REPLAYING HISTORY
            |--------------------------------------------------------------------------
            |
            | This is important.
            |
            | Example:
            |
            | Opening = 100
            | Oct 1  Stock Out = 20
            | Oct 5  Stock In  = 50
            |
            | If user selects Oct 5 only:
            |
            | Beginning Balance = 80
            | Stock In = 50
            | Remaining = 130
            |
            | We must NOT reset the balance to zero on Oct 5.
            |
            */

            $stockCard = $stockCard
                ->filter(function ($row) use ($dateFrom, $dateTo) {

                    if (!$row['date_key']) {
                        return !$dateFrom && !$dateTo;
                    }

                    if ($dateFrom && $row['date_key'] < $dateFrom) {
                        return false;
                    }

                    if ($dateTo && $row['date_key'] > $dateTo) {
                        return false;
                    }

                    return true;
                })
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | STOCK MOVEMENT TOTALS
        |--------------------------------------------------------------------------
        */

        $totalStockInQuantity = $stockCard->sum('in');

        $totalStockOutQuantity = $stockCard->sum('out');

        $totalStockInAmount = $stockCard
            ->where('in', '>', 0)
            ->sum(function ($row) {
                return (float) ($row['m']->amount ?? 0);
            });

        $totalStockOutAmount = $stockCard
            ->where('out', '>', 0)
            ->sum(function ($row) {
                return (float) ($row['m']->amount ?? 0);
            });

        /*
        |--------------------------------------------------------------------------
        | LOW / OUT OF STOCK
        |--------------------------------------------------------------------------
        */

        $stockStatusReport = $inventory
            ->filter(function ($item) {
                return $item['status'] !== 'In Stock';
            })
            ->sortBy([
                ['current_stock', 'asc'],
                ['product_name', 'asc'],
            ])
            ->values();

        $outOfStockCount = $inventory
            ->where('status', 'Out of Stock')
            ->count();

        $lowStockCount = $inventory
            ->where('status', 'Low Stock')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PURCHASE ORDERS
        |--------------------------------------------------------------------------
        */

        $purchaseOrders = PurchaseOrder::with([
                'supplier',
                'items',
            ])
            ->when(
                $dateFrom,
                fn ($query) =>
                    $query->whereDate(
                        'po_date',
                        '>=',
                        $dateFrom
                    )
            )
            ->when(
                $dateTo,
                fn ($query) =>
                    $query->whereDate(
                        'po_date',
                        '<=',
                        $dateTo
                    )
            )
            ->orderByDesc('po_date')
            ->orderByDesc('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER ORDERS
        |--------------------------------------------------------------------------
        */

        $customerOrders = CustomerOrder::with([
                'items',
                'payments',
            ])
            ->when(
                $dateFrom,
                fn ($query) =>
                    $query->whereDate(
                        'order_date',
                        '>=',
                        $dateFrom
                    )
            )
            ->when(
                $dateTo,
                fn ($query) =>
                    $query->whereDate(
                        'order_date',
                        '<=',
                        $dateTo
                    )
            )
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PRODUCT FILTER OPTIONS
        |--------------------------------------------------------------------------
        |
        | Only active products are selectable for a new stock card.
        | Archived/inactive products remain in the database and history.
        |
        */

        $productOptions = Product::where('status', 'active')
            ->orderBy('product_name')
            ->get([
                'id',
                'product_name',
                'brand',
            ]);

        /*
        |--------------------------------------------------------------------------
        | SELECTED PRODUCT
        |--------------------------------------------------------------------------
        */

        $selectedProduct = null;

        if ($productId) {

            $selectedProduct = Product::with([
                    'category',
                    'inventory',
                ])
                ->where('status', 'active')
                ->find($productId);
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('owner.reports', compact(
            'dateFrom',
            'dateTo',
            'productId',
            'productOptions',
            'selectedProduct',

            'totalProducts',
            'totalInventoryQuantity',
            'totalInventoryValue',

            'lowStockCount',
            'outOfStockCount',

            'totalStockInQuantity',
            'totalStockOutQuantity',
            'totalStockInAmount',
            'totalStockOutAmount',

            'inventory',
            'stockCard',
            'stockStatusReport',

            'purchaseOrders',
            'customerOrders'
        ));
    }
}