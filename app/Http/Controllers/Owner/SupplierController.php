<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DISPLAY SUPPLIERS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $suppliers = Supplier::orderBy('supplier_name')
            ->get();

        return view(
            'owner.suppliers',
            compact('suppliers')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADD SUPPLIER
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => [
                'required',
                'string',
                'max:255',
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:255',
            ],

            'contact_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        Supplier::create($validated);

        return redirect()
            ->route('owner.suppliers')
            ->with(
                'success',
                'Supplier added successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE SUPPLIER
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Supplier $supplier
    ) {
        $validated = $request->validate([
            'supplier_name' => [
                'required',
                'string',
                'max:255',
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:255',
            ],

            'contact_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('owner.suppliers')
            ->with(
                'success',
                'Supplier updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ARCHIVE SUPPLIER
    |--------------------------------------------------------------------------
    */

    public function archive(Supplier $supplier)
    {
        $supplier->update([
            'status' => 'archived',
        ]);

        return redirect()
            ->route('owner.suppliers')
            ->with(
                'success',
                'Supplier archived successfully.'
            );
    }
}