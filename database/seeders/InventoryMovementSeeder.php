<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryMovementSeeder extends Seeder
{
    public function run(): void
    {
        $owner = DB::table('users')
            ->where('username', 'owner')
            ->value('id');

        $secretary = DB::table('users')
            ->where('username', 'secretary')
            ->value('id');

        $products = DB::table('products')
            ->pluck('id', 'product_name');

        /*
         * Each movement is processed in order.
         * Inventory current_stock is updated together
         * with the movement so the Stock Card balance
         * remains consistent.
         */

        $movements = [
            // Shell Rimula R4 X
            [
                'product' => 'Shell Rimula R4 X',
                'type' => 'stock_in',
                'date' => '2026-09-01',
                'quantity' => 100,
                'party' => 'Shell Philippines',
                'cost' => 280,
                'reason' => 'Supplier delivery',
                'reference' => 'PO-2026-0001',
            ],
            [
                'product' => 'Shell Rimula R4 X',
                'type' => 'stock_out',
                'date' => '2026-09-03',
                'quantity' => -20,
                'party' => 'ABC Construction Supply',
                'price' => 350,
                'reason' => 'Customer delivery / release',
                'order' => 'CO-2026-0001',
                'receipt' => 'OR-2026-0001',
                'received_by' => 'Juan Dela Cruz',
            ],

            // Mobil Delvac MX
            [
                'product' => 'Mobil Delvac MX',
                'type' => 'stock_in',
                'date' => '2026-09-02',
                'quantity' => 80,
                'party' => 'Mobil Philippines',
                'cost' => 300,
                'reason' => 'Supplier delivery',
                'reference' => 'PO-2026-0003',
            ],
            [
                'product' => 'Mobil Delvac MX',
                'type' => 'stock_out',
                'date' => '2026-09-06',
                'quantity' => -20,
                'party' => 'Davao Equipment Services',
                'price' => 380,
                'reason' => 'Customer delivery / release',
                'order' => 'CO-2026-0002',
                'receipt' => 'OR-2026-0002',
                'received_by' => 'Pedro Santos',
            ],

            // Caltex Delo Gold
            [
                'product' => 'Caltex Delo Gold',
                'type' => 'stock_in',
                'date' => '2026-09-04',
                'quantity' => 50,
                'party' => 'Caltex Philippines',
                'cost' => 290,
                'reason' => 'Supplier delivery',
                'reference' => 'PO-2026-0002',
            ],
            [
                'product' => 'Caltex Delo Gold',
                'type' => 'stock_out',
                'date' => '2026-09-09',
                'quantity' => -15,
                'party' => 'Southern Hardware Trading',
                'price' => 360,
                'reason' => 'Customer delivery / release',
                'order' => 'CO-2026-0003',
                'receipt' => 'OR-2026-0003',
                'received_by' => 'Maria Garcia',
            ],

            // Shell Helix HX5
            [
                'product' => 'Shell Helix HX5',
                'type' => 'stock_in',
                'date' => '2026-09-05',
                'quantity' => 70,
                'party' => 'Shell Philippines',
                'cost' => 320,
                'reason' => 'Supplier delivery',
                'reference' => 'PO-2026-0001',
            ],
            [
                'product' => 'Shell Helix HX5',
                'type' => 'stock_out',
                'date' => '2026-09-18',
                'quantity' => -25,
                'party' => 'Mindanao Fleet Services',
                'price' => 430,
                'reason' => 'Customer delivery / release',
                'order' => 'CO-2026-0004',
                'receipt' => 'OR-2026-0004',
                'received_by' => 'Jose Reyes',
            ],

            // Petron
            [
                'product' => 'Petron Rev-X',
                'type' => 'stock_in',
                'date' => '2026-09-15',
                'quantity' => 20,
                'party' => 'Petron Corporation',
                'cost' => 250,
                'reason' => 'Supplier delivery',
                'reference' => 'PO-2026-0004',
            ],

            // Hydraulic Oil
            [
                'product' => 'Hydraulic Oil ISO 46',
                'type' => 'stock_in',
                'date' => '2026-09-16',
                'quantity' => 12,
                'party' => 'Mindanao Industrial Supply',
                'cost' => 220,
                'reason' => 'Supplier delivery',
                'reference' => 'PO-2026-0005',
            ],

            // Gear Oil
            [
                'product' => 'Gear Oil 80W-90',
                'type' => 'stock_in',
                'date' => '2026-09-17',
                'quantity' => 40,
                'party' => 'Eastern Petroleum',
                'cost' => 240,
                'reason' => 'Supplier delivery',
                'reference' => 'PO-2026-0006',
            ],
            [
                'product' => 'Gear Oil 80W-90',
                'type' => 'adjustment',
                'date' => '2026-09-25',
                'quantity' => -3,
                'party' => null,
                'reason' => 'Physical count adjustment',
                'reference' => 'Count Sheet - September 2026',
            ],

            // Grease
            [
                'product' => 'Lithium Grease EP2',
                'type' => 'stock_in',
                'date' => '2026-09-19',
                'quantity' => 30,
                'party' => 'Southern Lubricants Supply',
                'cost' => 190,
                'reason' => 'Supplier delivery',
                'reference' => 'PO-2026-0007',
            ],

            // Coolant
            [
                'product' => 'Long Life Coolant',
                'type' => 'stock_in',
                'date' => '2026-09-20',
                'quantity' => 5,
                'party' => 'Davao Auto Supply',
                'cost' => 220,
                'reason' => 'Supplier delivery',
                'reference' => 'PO-2026-0008',
            ],
        ];

        /*
         * Initialize all inventory records.
         */
        DB::table('inventories')->update([
            'current_stock' => 0,
            'updated_at' => now(),
        ]);

        /*
         * Process movements one by one.
         */
        foreach ($movements as $movement) {

            $productId = $products[$movement['product']];

            $inventory = DB::table('inventories')
                ->where('product_id', $productId)
                ->first();

            $stockBefore = (int) $inventory->current_stock;

            $quantity = (int) $movement['quantity'];

            $stockAfter = $stockBefore + $quantity;

            $unitCost = $movement['cost'] ?? null;
            $unitPrice = $movement['price'] ?? null;

            $amount = $unitPrice !== null
                ? abs($quantity) * $unitPrice
                : null;

            $userId = $movement['type'] === 'stock_in'
                ? $owner
                : $secretary;

            DB::table('inventories')
                ->where('product_id', $productId)
                ->update([
                    'current_stock' => $stockAfter,
                    'updated_at' => now(),
                ]);

            DB::table('inventory_movements')->insert([
                'product_id' => $productId,
                'user_id' => $userId,
                'movement_type' => $movement['type'],
                'transaction_date' => $movement['date'],
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'supplier_customer' => $movement['party'] ?? null,
                'unit_cost' => $unitCost,
                'unit_price' => $unitPrice,
                'amount' => $amount,
                'reason' => $movement['reason'] ?? null,
                'customer_order_reference' => $movement['order'] ?? null,
                'receipt_number' => $movement['receipt'] ?? null,
                'received_by' => $movement['received_by'] ?? null,
                'reference' => $movement['reference'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}