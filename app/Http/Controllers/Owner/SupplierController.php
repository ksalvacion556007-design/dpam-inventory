<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /*
     * Columns that belong to the suppliers table itself.
     */
    private const FIELDS = [
        'supplier_name',
        'contact_person',
        'contact_number',
        'email',
        'address',
        'status',
    ];


    /*
    |--------------------------------------------------------------------------
    | DISPLAY SUPPLIERS
    |--------------------------------------------------------------------------
    |
    | The page shows the ACTIVE suppliers by default. Inactive / archived
    | suppliers stay in the database and can be viewed with the status selector.
    |
    */

    public function index()
    {
        $suppliers = Supplier::with(['brands', 'products.category'])
            ->orderBy('supplier_name')
            ->get();

        $supplierRows = $suppliers->map(function (Supplier $supplier) {
            return [
                'id' => $supplier->id,
                'supplier_name' => $supplier->supplier_name,
                'contact_person' => $supplier->contact_person,
                'contact_number' => $supplier->contact_number,
                'email' => $supplier->email,
                'address' => $supplier->address,
                'status' => $supplier->status,

                // supplier_brands
                'brands' => $supplier->brands
                    ->pluck('brand_name')
                    ->unique()
                    ->sort()
                    ->values()
                    ->all(),

                // product_supplier -> products
                'products' => $supplier->products
                    ->sortBy('product_name')
                    ->map(fn ($product) => [
                        'id' => $product->id,
                        'name' => $product->product_name,
                        'brand' => $product->brand,
                        'category' => $product->category->category_name ?? null,
                        'status' => $product->status,
                    ])
                    ->values()
                    ->all(),
            ];
        })->values();

        // Active products that can be linked to a supplier.
        $productOptions = Product::sellable()
            ->orderBy('product_name')
            ->get(['id', 'product_name', 'brand'])
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->product_name,
                'brand' => $product->brand,
            ])
            ->values();

        // Brand suggestions: product brands + brands already used by suppliers.
        $brandOptions = Product::whereNotNull('brand')
            ->pluck('brand')
            ->merge(SupplierBrand::pluck('brand_name'))
            ->map(fn ($brand) => trim((string) $brand))
            ->filter()
            ->unique(fn ($brand) => mb_strtolower($brand))
            ->sort()
            ->values();

        return view(
            'owner.suppliers',
            compact('supplierRows', 'productOptions', 'brandOptions')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION (shared by add + update)
    |--------------------------------------------------------------------------
    */

    private function rules(): array
    {
        return [
            'supplier_name' => [
                'required',
                'string',
                'max:255',
            ],

            // NOT NULL in the suppliers migration, so required here.
            'contact_person' => [
                'required',
                'string',
                'max:255',
            ],

            'contact_number' => [
                'required',
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

            // Brands supplied (supplier_brands)
            'brands' => [
                'nullable',
                'array',
                'max:100',
            ],

            'brands.*' => [
                'nullable',
                'string',
                'max:255',
            ],

            // Products supplied (product_supplier)
            'product_ids' => [
                'nullable',
                'array',
            ],

            'product_ids.*' => [
                'integer',
                'exists:products,id',
            ],
        ];
    }


    /*
     * Saves the brands and products supplied.
     * Nothing else (POs, inventory, movements) is touched.
     */
    private function syncRelations(Supplier $supplier, array $data): void
    {
        $brands = collect($data['brands'] ?? [])
            ->map(fn ($brand) => trim((string) $brand))
            ->filter()
            ->unique(fn ($brand) => mb_strtolower($brand))
            ->values();

        // Remove only the brands the user removed in the form.
        $supplier->brands()
            ->whereNotIn('brand_name', $brands->all())
            ->delete();

        $existing = $supplier->brands()
            ->pluck('brand_name')
            ->map(fn ($brand) => mb_strtolower($brand));

        foreach ($brands as $brand) {
            if (!$existing->contains(mb_strtolower($brand))) {
                $supplier->brands()->create(['brand_name' => $brand]);
            }
        }

        $supplier->products()->sync(
            array_values(array_unique(array_map('intval', $data['product_ids'] ?? [])))
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADD SUPPLIER
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($validated) {
            $supplier = Supplier::create(
                Arr::only($validated, self::FIELDS)
            );

            $this->syncRelations($supplier, $validated);
        });

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
    |
    | Changing the status Active -> Inactive only changes the status.
    | Brands, products, Purchase Orders, inventory history and receipts
    | are never deleted.
    |
    */

    public function update(
        Request $request,
        Supplier $supplier
    ) {
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($validated, $request, $supplier) {
            $supplier->update(
                Arr::only($validated, self::FIELDS)
            );

            // Only touch the relationships when the form actually sent them.
            if ($request->boolean('relations_submitted')) {
                $this->syncRelations($supplier, $validated);
            }
        });

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