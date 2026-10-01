<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class StockCardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STOCK CARD
    |--------------------------------------------------------------------------
    |
    | The Stock Card is a historical view of inventory movements for
    | a selected product.
    |
    | It does NOT create, edit, or delete inventory.
    |
    | It reads the transactions recorded by InventoryController:
    |
    | Stock In     → RECEIVE
    | Stock Out    → SALE
    | Adjustment   → ADJUSTMENT
    |
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | AVAILABLE PRODUCTS
        |--------------------------------------------------------------------------
        |
        | Only active/non-archived products should be available for
        | selecting a Stock Card.
        |
        */

        $products = Product::with([
            'category',
            'inventory',
        ])
            ->where('status', '!=', 'archived')
            ->orderBy('product_name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | DEFAULT VALUES
        |--------------------------------------------------------------------------
        */

        $selectedProduct = null;

        $movements = collect();


        /*
        |--------------------------------------------------------------------------
        | SELECTED PRODUCT
        |--------------------------------------------------------------------------
        */

        if ($request->filled('product_id')) {

            $selectedProduct = Product::with([
                'category',
                'inventory',
            ])
                ->where('status', '!=', 'archived')
                ->findOrFail(
                    $request->product_id
                );


            /*
            |--------------------------------------------------------------------------
            | MOVEMENT QUERY
            |--------------------------------------------------------------------------
            |
            | The Stock Card uses inventory_movements as its source.
            |
            */

            $query = InventoryMovement::with([
                'product',
                'user',
            ])
                ->where(
                    'product_id',
                    $selectedProduct->id
                )
                ->orderBy('created_at')
                ->orderBy('id');


            /*
            |--------------------------------------------------------------------------
            | DATE FROM
            |--------------------------------------------------------------------------
            */

            if ($request->filled('date_from')) {

                $query->whereDate(
                    'created_at',
                    '>=',
                    $request->date_from
                );
            }


            /*
            |--------------------------------------------------------------------------
            | DATE TO
            |--------------------------------------------------------------------------
            */

            if ($request->filled('date_to')) {

                $query->whereDate(
                    'created_at',
                    '<=',
                    $request->date_to
                );
            }


            /*
            |--------------------------------------------------------------------------
            | GET MOVEMENTS
            |--------------------------------------------------------------------------
            */

            $movements = $query->get();
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN STOCK CARD VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'owner.stock-card',
            compact(
                'products',
                'selectedProduct',
                'movements'
            )
        );
    }
}