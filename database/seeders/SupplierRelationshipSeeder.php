<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierRelationshipSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = DB::table('suppliers')
            ->pluck('id', 'supplier_name');

        $products = DB::table('products')
            ->pluck('id', 'product_name');

        /*
        |--------------------------------------------------------------------------
        | Supplier Brands
        |--------------------------------------------------------------------------
        | This records which brands each supplier supplies to DPAM.
        |
        | A supplier can supply multiple brands.
        | The same brand can be supplied by multiple suppliers.
        |--------------------------------------------------------------------------
        */

        $supplierBrands = [
            'Petron Corporation' => [
                'Petron',
            ],

            'Shell Philippines' => [
                'Shell',
            ],

            'Caltex Philippines' => [
                'Caltex',
            ],

            'Mobil Philippines' => [
                'Mobil',
            ],

            'Castrol Philippines' => [
                'Castrol',
            ],

            'Phoenix Petroleum' => [
                'Phoenix',
            ],

            'TotalEnergies Philippines' => [
                'TotalEnergies',
            ],

            'Eastern Petroleum' => [
                'Shell',
                'Mobil',
                'Industrial Lubricants',
            ],

            'Mindanao Industrial Supply' => [
                'Hydraulic Oils',
                'Industrial Lubricants',
            ],

            'Southern Lubricants Supply' => [
                'Industrial Lubricants',
                'Grease',
            ],

            'Davao Auto Supply' => [
                'Petron',
                'Castrol',
            ],

            'Mindanao Oil Trading' => [
                'Shell',
                'Caltex',
                'Mobil',
            ],
        ];

        foreach ($supplierBrands as $supplierName => $brands) {
            if (!isset($suppliers[$supplierName])) {
                continue;
            }

            foreach ($brands as $brandName) {
                DB::table('supplier_brands')->insert([
                    'supplier_id' => $suppliers[$supplierName],
                    'brand_name' => $brandName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Product-Supplier Relationships
        |--------------------------------------------------------------------------
        | A product may have multiple suppliers.
        | A supplier may supply many products.
        |--------------------------------------------------------------------------
        */

        $productSuppliers = [
            'Shell Rimula R4 X' => [
                'Shell Philippines',
                'Eastern Petroleum',
                'Mindanao Oil Trading',
            ],

            'Shell Rimula R4 X 5L' => [
                'Shell Philippines',
                'Mindanao Oil Trading',
            ],

            'Shell Rimula R5 E' => [
                'Shell Philippines',
                'Eastern Petroleum',
            ],

            'Shell Helix HX5' => [
                'Shell Philippines',
                'Mindanao Oil Trading',
            ],

            'Caltex Delo Gold' => [
                'Caltex Philippines',
                'Mindanao Oil Trading',
            ],

            'Caltex Havoline' => [
                'Caltex Philippines',
                'Mindanao Oil Trading',
            ],

            'Mobil Delvac MX' => [
                'Mobil Philippines',
                'Eastern Petroleum',
                'Mindanao Oil Trading',
            ],

            'Mobil Delvac 1' => [
                'Mobil Philippines',
                'Eastern Petroleum',
            ],

            'Castrol GTX' => [
                'Castrol Philippines',
                'Davao Auto Supply',
            ],

            'Petron Rev-X' => [
                'Petron Corporation',
                'Davao Auto Supply',
            ],

            'Hydraulic Oil ISO 46' => [
                'Mindanao Industrial Supply',
            ],

            'Hydraulic Oil ISO 68' => [
                'Mindanao Industrial Supply',
            ],

            'Gear Oil 80W-90' => [
                'Eastern Petroleum',
                'Mindanao Industrial Supply',
            ],

            'Gear Oil 85W-140' => [
                'Eastern Petroleum',
                'Mindanao Industrial Supply',
            ],

            'ATF Dexron III' => [
                'Mindanao Industrial Supply',
                'Davao Auto Supply',
            ],

            'Brake Fluid DOT 3' => [
                'Davao Auto Supply',
            ],

            'Lithium Grease EP2' => [
                'Southern Lubricants Supply',
                'Mindanao Industrial Supply',
            ],

            'Multipurpose Grease' => [
                'Southern Lubricants Supply',
            ],

            'Long Life Coolant' => [
                'Davao Auto Supply',
                'Mindanao Industrial Supply',
            ],

            'Industrial Lubricant ISO 220' => [
                'Mindanao Industrial Supply',
                'Southern Lubricants Supply',
            ],
        ];

        foreach ($productSuppliers as $productName => $supplierNames) {
            if (!isset($products[$productName])) {
                continue;
            }

            foreach ($supplierNames as $supplierName) {
                if (!isset($suppliers[$supplierName])) {
                    continue;
                }

                DB::table('product_supplier')->insert([
                    'product_id' => $products[$productName],
                    'supplier_id' => $suppliers[$supplierName],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}