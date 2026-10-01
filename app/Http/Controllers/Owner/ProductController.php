<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // =====================================================
    // DISPLAY PRODUCTS
    // =====================================================

    public function index()
    {
        $products = Product::with('category')
            ->where('status', '!=', 'archived')
            ->orderBy('product_name')
            ->get();

        $categories = Category::where('status', 'active')
            ->orderBy('category_name')
            ->get();

        return view('owner.products', compact(
            'products',
            'categories'
        ));
    }


    // =====================================================
    // ADD NEW PRODUCT
    // =====================================================

    public function store(Request $request)
    {
        $validated = $request->validate([

            'product_name' => [
                'required',
                'string',
                'max:255'
            ],

            'category_id' => [
                'required',
                'exists:categories,id'
            ],

            'api' => [
                'nullable',
                'string',
                'max:255'
            ],

            'base_oil' => [
                'nullable',
                'string',
                'max:255'
            ],

            'package_size' => [
                'nullable',
                'string',
                'max:100'
            ],

            'unit' => [
                'required',
                'string',
                'max:50'
            ],

            'unit_price' => [
                'required',
                'numeric',
                'min:0'
            ],

            'reorder_level' => [
                'required',
                'integer',
                'min:0'
            ],

            'status' => [
                'required',
                'in:active,inactive'
            ],

        ]);


        Product::create($validated);


        return redirect()
            ->route('owner.products')
            ->with('success', 'Product added successfully.');
    }


    // =====================================================
    // UPDATE PRODUCT
    // =====================================================

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([

            'product_name' => [
                'required',
                'string',
                'max:255'
            ],

            'category_id' => [
                'required',
                'exists:categories,id'
            ],

            'api' => [
                'nullable',
                'string',
                'max:255'
            ],

            'base_oil' => [
                'nullable',
                'string',
                'max:255'
            ],

            'package_size' => [
                'nullable',
                'string',
                'max:100'
            ],

            'unit' => [
                'required',
                'string',
                'max:50'
            ],

            'unit_price' => [
                'required',
                'numeric',
                'min:0'
            ],

            'reorder_level' => [
                'required',
                'integer',
                'min:0'
            ],

            'status' => [
                'required',
                'in:active,inactive'
            ],

        ]);


        $product->update($validated);


        return redirect()
            ->route('owner.products')
            ->with('success', 'Product updated successfully.');
    }


    // =====================================================
    // ARCHIVE PRODUCT
    // =====================================================

    public function archive(Product $product)
    {
        $product->update([
            'status' => 'archived',
        ]);


        return redirect()
            ->route('owner.products')
            ->with('success', 'Product archived successfully.');
    }
}