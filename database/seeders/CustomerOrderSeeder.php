<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerOrderSeeder extends Seeder
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

        $orders = [
            /*
            |--------------------------------------------------------------------------
            | 1. CONFIRMED
            |--------------------------------------------------------------------------
            */
            [
                'number' => 'CO-2026-0001',
                'customer' => 'ABC Construction Supply',
                'contact' => '09170000001',
                'date' => '2026-09-01',
                'status' => 'confirmed',
                'check' => 'available',
                'decision' => 'confirmed',
                'product' => 'Shell Rimula R4 X',
                'quantity' => 20,
                'reserved' => 20,
                'fulfilled' => 0,
            ],

            /*
            |--------------------------------------------------------------------------
            | 2. PARTIALLY FULFILLED
            |--------------------------------------------------------------------------
            */
            [
                'number' => 'CO-2026-0002',
                'customer' => 'Davao Equipment Services',
                'contact' => '09170000002',
                'date' => '2026-09-05',
                'status' => 'partially_fulfilled',
                'check' => 'available',
                'decision' => 'confirmed',
                'product' => 'Mobil Delvac MX',
                'quantity' => 30,
                'reserved' => 10,
                'fulfilled' => 20,
            ],

            /*
            |--------------------------------------------------------------------------
            | 3. CONFIRMED
            |--------------------------------------------------------------------------
            | This replaces the old ready_for_delivery status.
            |--------------------------------------------------------------------------
            */
            [
                'number' => 'CO-2026-0003',
                'customer' => 'Southern Hardware Trading',
                'contact' => '09170000003',
                'date' => '2026-09-08',
                'status' => 'confirmed',
                'check' => 'available',
                'decision' => 'confirmed',
                'product' => 'Caltex Delo Gold',
                'quantity' => 15,
                'reserved' => 15,
                'fulfilled' => 0,
            ],

            /*
            |--------------------------------------------------------------------------
            | 4. FULFILLED
            |--------------------------------------------------------------------------
            */
            [
                'number' => 'CO-2026-0004',
                'customer' => 'Mindanao Fleet Services',
                'contact' => '09170000004',
                'date' => '2026-09-10',
                'status' => 'fulfilled',
                'check' => 'available',
                'decision' => 'confirmed',
                'product' => 'Shell Helix HX5',
                'quantity' => 25,
                'reserved' => 0,
                'fulfilled' => 25,
            ],

            /*
            |--------------------------------------------------------------------------
            | 5. PENDING INVENTORY CHECK
            |--------------------------------------------------------------------------
            */
            [
                'number' => 'CO-2026-0005',
                'customer' => 'Davao Industrial Works',
                'contact' => '09170000005',
                'date' => '2026-09-15',
                'status' => 'pending_inventory_check',
                'check' => 'pending',
                'decision' => null,
                'product' => 'Hydraulic Oil ISO 46',
                'quantity' => 40,
                'reserved' => 0,
                'fulfilled' => 0,
            ],

            /*
            |--------------------------------------------------------------------------
            | 6. CANCELLED
            |--------------------------------------------------------------------------
            */
            [
                'number' => 'CO-2026-0006',
                'customer' => 'Eastern Transport Services',
                'contact' => '09170000006',
                'date' => '2026-09-18',
                'status' => 'cancelled',
                'check' => 'insufficient',
                'decision' => 'cancelled',
                'product' => 'Mobil Delvac 1',
                'quantity' => 50,
                'reserved' => 0,
                'fulfilled' => 0,
            ],
        ];

        foreach ($orders as $order) {

            /*
            |--------------------------------------------------------------------------
            | Verify Product Exists
            |--------------------------------------------------------------------------
            */
            if (!isset($products[$order['product']])) {
                throw new \RuntimeException(
                    "Product '{$order['product']}' was not found in the products table."
                );
            }

            $productId = $products[$order['product']];

            /*
            |--------------------------------------------------------------------------
            | Inventory Check Date
            |--------------------------------------------------------------------------
            */
            $checkedAt = $order['check'] === 'pending'
                ? null
                : now()->subDays(5);

            /*
            |--------------------------------------------------------------------------
            | Product Unit Price
            |--------------------------------------------------------------------------
            */
            $unitPrice = DB::table('products')
                ->where('id', $productId)
                ->value('unit_price');

            /*
            |--------------------------------------------------------------------------
            | Create Customer Order
            |--------------------------------------------------------------------------
            */
            $orderId = DB::table('customer_orders')->insertGetId([
                'order_number' => $order['number'],

                'customer_name' => $order['customer'],

                'customer_contact' => $order['contact'],

                'order_date' => $order['date'],

                'status' => $order['status'],

                'inventory_check_status' => $order['check'],

                'inventory_checked_by' =>
                    $order['check'] === 'pending'
                        ? null
                        : $secretary,

                'inventory_checked_at' => $checkedAt,

                'inventory_check_notes' =>
                    $order['check'] === 'available'
                        ? 'Product availability checked by the Secretary.'
                        : (
                            $order['check'] === 'insufficient'
                                ? 'Insufficient stock based on inventory check.'
                                : null
                        ),

                'owner_decision' => $order['decision'],

                'notes' => null,

                'user_id' => $owner,

                'created_at' => now(),

                'updated_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Customer Order Item
            |--------------------------------------------------------------------------
            */
            DB::table('customer_order_items')->insert([
                'customer_order_id' => $orderId,

                'product_id' => $productId,

                'quantity' => $order['quantity'],

                'reserved_quantity' => $order['reserved'],

                'fulfilled_quantity' => $order['fulfilled'],

                'unit_price' => $unitPrice,

                'subtotal' =>
                    $order['quantity'] * $unitPrice,

                'created_at' => now(),

                'updated_at' => now(),
            ]);
        }
    }
}