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

        $orders = DB::table('customer_orders')
            ->pluck('id', 'order_number');

        $purchaseOrders = [
            [
                'number' => 'PO-2026-0001',
                'supplier' => 'Shell Philippines',
                'customer_order' => 'CO-2026-0005',
                'date' => '2026-09-15',
                'status' => 'pending',
                'product' => 'Shell Rimula R4 X',
                'quantity' => 50,
                'cost' => 280,
            ],
            [
                'number' => 'PO-2026-0002',
                'supplier' => 'Caltex Philippines',
                'customer_order' => null,
                'date' => '2026-09-10',
                'status' => 'approved',
                'product' => 'Caltex Delo Gold',
                'quantity' => 40,
                'cost' => 290,
            ],
            [
                'number' => 'PO-2026-0003',
                'supplier' => 'Mobil Philippines',
                'customer_order' => null,
                'date' => '2026-09-12',
                'status' => 'received',
                'product' => 'Mobil Delvac MX',
                'quantity' => 60,
                'cost' => 300,
            ],
            [
                'number' => 'PO-2026-0004',
                'supplier' => 'Petron Corporation',
                'customer_order' => null,
                'date' => '2026-09-14',
                'status' => 'partially_received',
                'product' => 'Petron Rev-X',
                'quantity' => 50,
                'cost' => 250,
            ],
            [
                'number' => 'PO-2026-0005',
                'supplier' => 'Castrol Philippines',
                'customer_order' => null,
                'date' => '2026-09-20',
                'status' => 'draft',
                'product' => 'Castrol GTX',
                'quantity' => 30,
                'cost' => 330,
            ],
            [
                'number' => 'PO-2026-0006',
                'supplier' => 'Caltex Philippines',
                'customer_order' => null,
                'date' => '2026-09-21',
                'status' => 'cancelled',
                'product' => 'Caltex Havoline',
                'quantity' => 25,
                'cost' => 310,
            ],
        ];

        foreach ($purchaseOrders as $po) {

            $purchaseOrderId = DB::table('purchase_orders')->insertGetId([
                'po_number' => $po['number'],
                'supplier_id' => $suppliers[$po['supplier']],
                'customer_order_id' => $po['customer_order']
                    ? $orders[$po['customer_order']]
                    : null,
                'po_date' => $po['date'],
                'status' => $po['status'],
                'notes' => null,
                'user_id' => $owner,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $productId = $products[$po['product']];

            DB::table('purchase_order_items')->insert([
                'purchase_order_id' => $purchaseOrderId,
                'product_id' => $productId,
                'quantity' => $po['quantity'],
                'unit_cost' => $po['cost'],
                'subtotal' => $po['quantity'] * $po['cost'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}