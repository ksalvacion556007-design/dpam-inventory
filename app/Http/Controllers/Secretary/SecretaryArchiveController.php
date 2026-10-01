<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SecretaryArchiveController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $type = $request->input('type', 'all');

        /*
        |--------------------------------------------------------------------------
        | Archived Suppliers
        |--------------------------------------------------------------------------
        */

        $archivedSuppliers = Supplier::query()
            ->where('status', 'archived')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('supplier_name', 'like', '%' . $search . '%')
                        ->orWhere('contact_person', 'like', '%' . $search . '%')
                        ->orWhere('contact_number', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('supplier_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Inactive Products
        |--------------------------------------------------------------------------
        |
        | Products currently only support active/inactive in the database.
        | Therefore inactive products are displayed separately from archived
        | suppliers and are NOT labeled as archived.
        |
        */

        $inactiveProducts = Product::with([
            'category',
            'inventory',
        ])
            ->where('status', 'inactive')
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
        | Counts
        |--------------------------------------------------------------------------
        */

        $archivedSupplierCount = Supplier::where(
            'status',
            'archived'
        )->count();

        $inactiveProductCount = Product::where(
            'status',
            'inactive'
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'secretary.secretary-archive',
            compact(
                'search',
                'type',
                'archivedSuppliers',
                'inactiveProducts',
                'archivedSupplierCount',
                'inactiveProductCount'
            )
        );
    }
}