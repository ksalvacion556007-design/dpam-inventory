<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;

class SecretaryStockCardController extends Controller
{
    /**
     * Display the Secretary Stock Card.
     *
     * The Stock Card is read-only.
     *
     * Data comes from inventory_movements.
     *
     * Stock In  = RECEIVE
     * Stock Out = SALE
     * Adjustment = ADJUSTMENT
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        $search = trim($request->input('search', ''));

        /*
        |--------------------------------------------------------------------------
        | SELECTED PRODUCT
        |--------------------------------------------------------------------------
        |
        | The user can select a product to view its Stock Card.
        |
        */

        $selectedProductId = $request->input('product_id');

        /*
        |--------------------------------------------------------------------------
        | PRODUCTS
        |--------------------------------------------------------------------------
        |
        | Only active products are shown in the product selector.
        |
        */

        $products = Product::with([
            'category',
            'inventory',
        ])
            ->where('status', 'active')
            ->when($search !== '', function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('product_name', 'like', '%' . $search . '%')
                        ->orWhere('api', 'like', '%' . $search . '%')
                        ->orWhere('base_oil', 'like', '%' . $search . '%');

                });

            })
            ->orderBy('product_name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | SELECTED PRODUCT
        |--------------------------------------------------------------------------
        */

        $selectedProduct = null;

        if ($selectedProductId) {

            $selectedProduct = Product::with([
                'category',
                'inventory',
            ])->find($selectedProductId);

        }


        /*
        |--------------------------------------------------------------------------
        | STOCK CARD MOVEMENTS
        |--------------------------------------------------------------------------
        |
        | The Stock Card is based on actual inventory movements.
        |
        | We use transaction_date instead of created_at because
        | transaction_date represents the actual business transaction date.
        |
        */

        $movements = collect();

        if ($selectedProduct) {

            $movements = InventoryMovement::with([
                'user',
            ])
                ->where('product_id', $selectedProduct->id)
                ->orderBy('transaction_date')
                ->orderBy('id')
                ->get();

        }


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $currentStock = 0;

        if ($selectedProduct) {

            $currentStock = $selectedProduct->inventory
                ? (int) $selectedProduct->inventory->current_stock
                : 0;

        }


        $totalReceived = $movements
            ->where('movement_type', 'stock_in')
            ->sum(function ($movement) {

                return abs((int) $movement->quantity);

            });


        $totalReleased = $movements
            ->where('movement_type', 'stock_out')
            ->sum(function ($movement) {

                return abs((int) $movement->quantity);

            });


        $adjustmentQuantity = $movements
            ->where('movement_type', 'adjustment')
            ->sum(function ($movement) {

                return (int) $movement->quantity;

            });


        return view(
            'secretary.secretary-stock-card',
            compact(
                'products',
                'selectedProduct',
                'movements',
                'search',
                'selectedProductId',
                'currentStock',
                'totalReceived',
                'totalReleased',
                'adjustmentQuantity'
            )
        );
    }
}