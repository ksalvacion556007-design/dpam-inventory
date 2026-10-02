<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        $owner = DB::table('users')
            ->where('username', 'owner')
            ->value('id');

        $suppliers = DB::table('suppliers')
            ->pluck('id', 'supplier_name');

        $products = DB::table('products')
            ->pluck('id', 'product_name');

        $customerOrders = DB::table('customer_orders')
            ->pluck('id', 'order_number');

        $purchaseOrders = [

            /*
            |--------------------------------------------------------------------------
            | 1. PENDING PO FOR CUSTOMER ORDER
            |--------------------------------------------------------------------------
            | Not yet approved.
            | No supplier delivery has been recorded.
            |--------------------------------------------------------------------------
            */
            [
                'number' => 'PO-2026-0001',
                'supplier' => 'Shell Philippines',
                'customer_order' => 'CO-2026-0005',
                'date' => '2026-09-15',
                'status' => 'pending',
                'product' => 'Shell Rimula R4 X',
                'quantity' => 50,
                'received_quantity' => 0,
                'cost' => 280.00,
            ],

            /*
            |--------------------------------------------------------------------------
            | 2. APPROVED PO
            |--------------------------------------------------------------------------
            | Owner approved the PO.
            | Waiting for actual supplier delivery.
            |--------------------------------------------------------------------------
            */
            [
                'number' => 'PO-2026-0002',
                'supplier' => 'Caltex Philippines',
                'customer_order' => null,
                'date' => '2026-09-10',
                'status' => 'approved',
                'product' => 'Caltex Delo Gold',
                'quantity' => 40,
                'received_quantity' => 0,
                'cost' => 290.00,
            ],

            /*
            |--------------------------------------------------------------------------
            | 3. RECEIVED PO
            |--------------------------------------------------------------------------
            | Test record showing that the entire ordered quantity
            | has already been received.
            |
            | IMPORTANT:
            | This is seed/test data only.
            | It does NOT increase inventory.
            |--------------------------------------------------------------------------
            */
            [
                'number' => 'PO-2026-0003',
                'supplier' => 'Mobil Philippines',
                'customer_order' => null,
                'date' => '2026-09-12',
                'status' => 'received',
                'product' => 'Mobil Delvac MX',
                'quantity' => 60,
                'received_quantity' => 60,
                'cost' => 300.00,
            ],

            /*
            |--------------------------------------------------------------------------
            | 4. PARTIALLY RECEIVED PO
            |--------------------------------------------------------------------------
            | Test record showing a partial supplier delivery.
            |
            | Ordered: 50
            | Received: 25
            | Remaining: 25
            |--------------------------------------------------------------------------
            */
            [
                'number' => 'PO-2026-0004',
                'supplier' => 'Petron Corporation',
                'customer_order' => null,
                'date' => '2026-09-14',
                'status' => 'partially_received',
                'product' => 'Petron Rev-X',
                'quantity' => 50,
                'received_quantity' => 25,
                'cost' => 250.00,
            ],

            /*
            |--------------------------------------------------------------------------
            | 5. DRAFT PO
            |--------------------------------------------------------------------------
            */
            [
                'number' => 'PO-2026-0005',
                'supplier' => 'Castrol Philippines',
                'customer_order' => null,
                'date' => '2026-09-20',
                'status' => 'draft',
                'product' => 'Castrol GTX',
                'quantity' => 30,
                'received_quantity' => 0,
                'cost' => 330.00,
            ],

            /*
            |--------------------------------------------------------------------------
            | 6. CANCELLED PO
            |--------------------------------------------------------------------------
            */
            [
                'number' => 'PO-2026-0006',
                'supplier' => 'Caltex Philippines',
                'customer_order' => null,
                'date' => '2026-09-21',
                'status' => 'cancelled',
                'product' => 'Caltex Havoline',
                'quantity' => 25,
                'received_quantity' => 0,
                'cost' => 310.00,
            ],
        ];

        foreach ($purchaseOrders as $po) {

            /*
            |--------------------------------------------------------------------------
            | Verify Supplier
            |--------------------------------------------------------------------------
            */
            if (!isset($suppliers[$po['supplier']])) {
                throw new \RuntimeException(
                    "Supplier '{$po['supplier']}' was not found in the suppliers table."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Verify Product
            |--------------------------------------------------------------------------
            */
            if (!isset($products[$po['product']])) {
                throw new \RuntimeException(
                    "Product '{$po['product']}' was not found in the products table."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Verify Customer Order When Provided
            |--------------------------------------------------------------------------
            */
            $customerOrderId = null;

            if ($po['customer_order'] !== null) {

                if (!isset($customerOrders[$po['customer_order']])) {
                    throw new \RuntimeException(
                        "Customer order '{$po['customer_order']}' was not found in the customer_orders table."
                    );
                }

                $customerOrderId =
                    $customerOrders[$po['customer_order']];
            }

            /*
            |--------------------------------------------------------------------------
            | Create Purchase Order
            |--------------------------------------------------------------------------
            */
            $purchaseOrderId = DB::table('purchase_orders')
                ->insertGetId([
                    'po_number' => $po['number'],

                    'supplier_id' =>
                        $suppliers[$po['supplier']],

                    'customer_order_id' =>
                        $customerOrderId,

                    'po_date' =>
                        $po['date'],

                    'status' =>
                        $po['status'],

                    'notes' => null,

                    'user_id' =>
                        $owner,

                    'created_at' =>
                        now(),

                    'updated_at' =>
                        now(),
                ]);

            /*
            |--------------------------------------------------------------------------
            | Create Purchase Order Item
            |--------------------------------------------------------------------------
            */
            $productId = $products[$po['product']];

            DB::table('purchase_order_items')
                ->insert([
                    'purchase_order_id' =>
                        $purchaseOrderId,

                    'product_id' =>
                        $productId,

                    'quantity' =>
                        $po['quantity'],

                    'received_quantity' =>
                        $po['received_quantity'],

                    'unit_cost' =>
                        $po['cost'],

                    'subtotal' =>
                        $po['quantity'] * $po['cost'],

                    'created_at' =>
                        now(),

                    'updated_at' =>
                        now(),
                ]);
        }
    }
}