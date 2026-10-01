<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            SupplierSeeder::class,
            InventorySeeder::class,
            CustomerOrderSeeder::class,
            PurchaseOrderSeeder::class,
            PaymentSeeder::class,
            InventoryMovementSeeder::class,
            ActivityLogSeeder::class,
        ]);
    }
}