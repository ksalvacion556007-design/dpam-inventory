<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,category_name'
            ],
            'status' => [
                'required',
                'in:active,inactive'
            ],
        ]);

        Category::create($validated);

        return redirect()
            ->route('owner.products')
            ->with('success', 'Category added successfully.');
    }
}