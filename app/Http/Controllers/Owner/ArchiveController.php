<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | ARCHIVED PRODUCTS
        |--------------------------------------------------------------------------
        |
        | Products that are no longer active.
        |
        */

        $archivedProducts = Product::with('category')
            ->where('status', 'archived')
            ->orderBy('product_name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ARCHIVED SUPPLIERS
        |--------------------------------------------------------------------------
        |
        | Suppliers that are no longer active.
        |
        */

        $archivedSuppliers = Supplier::where('status', 'archived')
            ->orderBy('supplier_name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | COUNTS
        |--------------------------------------------------------------------------
        */

        $archivedProductCount = $archivedProducts->count();

        $archivedSupplierCount = $archivedSuppliers->count();


        /*
        |--------------------------------------------------------------------------
        | ARCHIVE VIEW
        |--------------------------------------------------------------------------
        */

        return view('owner.archive', [
            'archivedProducts' => $archivedProducts,
            'archivedSuppliers' => $archivedSuppliers,
            'archivedProductCount' => $archivedProductCount,
            'archivedSupplierCount' => $archivedSupplierCount,
        ]);
    }
}