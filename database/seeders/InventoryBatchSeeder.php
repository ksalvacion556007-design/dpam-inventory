<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryBatchSeeder extends Seeder
{
    public function run(): void
    {
        $products = DB::table('products')
            ->pluck('id', 'product_name');

        $suppliers = DB::table('suppliers')
            ->pluck('id', 'supplier_name');

        $purchaseOrders = DB::table('purchase_orders')
            ->pluck('id', 'po_number');

        $batches = [
            [
                'product' => 'Shell Rimula R4 X',
                'supplier' => 'Shell Philippines',
                'po' => 'PO-2026-0001',
                'batch_number' => 'SR4X-2026-001',
                'received_date' => '2026-09-01',
                'expiration_date' => '2028-09-01',
                'best_before_date' => null,
                'quantity_received' => 100,
                'quantity_remaining' => 80,
                'unit_cost' => 280,
            ],

            [
                'product' => 'Mobil Delvac MX',
                'supplier' => 'Mobil Philippines',
                'po' => 'PO-2026-0003',
                'batch_number' => 'DMX-2026-001',
                'received_date' => '2026-09-02',
                'expiration_date' => '2029-06-30',
                'best_before_date' => null,
                'quantity_received' => 80,
                'quantity_remaining' => 60,
                'unit_cost' => 300,
            ],

            [
                'product' => 'Caltex Delo Gold',
                'supplier' => 'Caltex Philippines',
                'po' => 'PO-2026-0002',
                'batch_number' => 'CDG-2026-001',
                'received_date' => '2026-09-04',
                'expiration_date' => '2028-09-04',
                'best_before_date' => null,
                'quantity_received' => 50,
                'quantity_remaining' => 35,
                'unit_cost' => 290,
            ],

            [
                'product' => 'Shell Helix HX5',
                'supplier' => 'Shell Philippines',
                'po' => 'PO-2026-0001',
                'batch_number' => 'HX5-2026-001',
                'received_date' => '2026-09-05',
                'expiration_date' => '2028-09-05',
                'best_before_date' => null,
                'quantity_received' => 70,
                'quantity_remaining' => 45,
                'unit_cost' => 320,
            ],

            [
                'product' => 'Petron Rev-X',
                'supplier' => 'Petron Corporation',
                'po' => 'PO-2026-0004',
                'batch_number' => 'PRX-2026-001',
                'received_date' => '2026-09-15',
                'expiration_date' => '2028-09-15',
                'best_before_date' => null,
                'quantity_received' => 20,
                'quantity_remaining' => 20,
                'unit_cost' => 250,
            ],

            [
                'product' => 'Hydraulic Oil ISO 46',
                'supplier' => 'Mindanao Industrial Supply',
                'po' => 'PO-2026-0005',
                'batch_number' => 'H46-2026-001',
                'received_date' => '2026-09-16',
                'expiration_date' => null,
                'best_before_date' => '2029-09-16',
                'quantity_received' => 12,
                'quantity_remaining' => 12,
                'unit_cost' => 220,
            ],

            [
                'product' => 'Gear Oil 80W-90',
                'supplier' => 'Eastern Petroleum',
                'po' => 'PO-2026-0006',
                'batch_number' => 'GO8090-2026-001',
                'received_date' => '2026-09-17',
                'expiration_date' => null,
                'best_before_date' => '2029-09-17',
                'quantity_received' => 40,
                'quantity_remaining' => 37,
                'unit_cost' => 240,
            ],

            [
                'product' => 'Lithium Grease EP2',
                'supplier' => 'Southern Lubricants Supply',
                'po' => 'PO-2026-0007',
                'batch_number' => 'LGE2-2026-001',
                'received_date' => '2026-09-19',
                'expiration_date' => '2029-09-19',
                'best_before_date' => null,
                'quantity_received' => 30,
                'quantity_remaining' => 30,
                'unit_cost' => 190,
            ],

            [
                'product' => 'Long Life Coolant',
                'supplier' => 'Davao Auto Supply',
                'po' => 'PO-2026-0008',
                'batch_number' => 'LLC-2026-001',
                'received_date' => '2026-09-20',
                'expiration_date' => '2028-03-20',
                'best_before_date' => null,
                'quantity_received' => 5,
                'quantity_remaining' => 5,
                'unit_cost' => 220,
            ],
        ];

        foreach ($batches as $batch) {
            $productId =
                $products[$batch['product']]
                ?? null;

            if (!$productId) {
                $this->command->warn(
                    "Product '{$batch['product']}' not found. Batch skipped."
                );

                continue;
            }

            $supplierId =
                $suppliers[$batch['supplier']]
                ?? null;

            $purchaseOrderId =
                $purchaseOrders[$batch['po']]
                ?? null;

            DB::table('inventory_batches')->updateOrInsert(
                [
                    'product_id' =>
                        $productId,

                    'batch_number' =>
                        $batch['batch_number'],
                ],
                [
                    'supplier_id' =>
                        $supplierId,

                    'purchase_order_id' =>
                        $purchaseOrderId,

                    'received_date' =>
                        $batch['received_date'],

                    'expiration_date' =>
                        $batch['expiration_date'],

                    'best_before_date' =>
                        $batch['best_before_date'],

                    'quantity_received' =>
                        $batch['quantity_received'],

                    'quantity_remaining' =>
                        $batch['quantity_remaining'],

                    'unit_cost' =>
                        $batch['unit_cost'],

                    'status' =>
                        $batch['quantity_remaining'] > 0
                            ? 'active'
                            : 'depleted',

                    'updated_at' =>
                        now(),

                    'created_at' =>
                        now(),
                ]
            );
        }
    }
}