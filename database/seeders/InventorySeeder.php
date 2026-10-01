<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $products = DB::table('products')->pluck('id');

        foreach ($products as $productId) {
            DB::table('inventories')->insert([
                'product_id' => $productId,
                'current_stock' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}