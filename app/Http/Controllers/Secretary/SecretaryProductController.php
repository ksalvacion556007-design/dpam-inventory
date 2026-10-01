<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SecretaryProductController extends Controller
{
    /**
     * Display products and, when selected,
     * display the product details on the same page.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $selectedProductId = $request->input('product');

        /*
        |--------------------------------------------------------------------------
        | Product List
        |--------------------------------------------------------------------------
        */

        $products = Product::with([
            'category',
            'inventory',
        ])
            ->where('status', 'active')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {

                    $query->where(
                        'product_name',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'api',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'base_oil',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'package_size',
                        'like',
                        '%' . $search . '%'
                    );
                });
            })
            ->orderBy('product_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Selected Product
        |--------------------------------------------------------------------------
        */

        $selectedProduct = null;

        if ($selectedProductId) {

            $selectedProduct = Product::with([
                'category',
                'inventory',
                'inventoryMovements' => function ($query) {
                    $query
                        ->orderByDesc('transaction_date')
                        ->orderByDesc('id')
                        ->take(10);
                },
            ])
                ->where('status', 'active')
                ->find($selectedProductId);
        }

        /*
        |--------------------------------------------------------------------------
        | Return Single Secretary Products Blade
        |--------------------------------------------------------------------------
        */

        return view(
            'secretary.secretary-products',
            compact(
                'products',
                'search',
                'selectedProduct'
            )
        );
    }
}