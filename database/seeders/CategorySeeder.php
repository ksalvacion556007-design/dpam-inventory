<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Engine Oil',
            'Gear Oil',
            'Hydraulic Oil',
            'Transmission Fluid',
            'Brake Fluid',
            'Grease',
            'Coolant',
            'Industrial Lubricants',
            'Diesel Oil',
            'Gasoline Oil',
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'category_name' => $category,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}