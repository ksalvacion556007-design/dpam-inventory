<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $cashier = DB::table('users')
            ->where('username', 'cashier')
            ->value('id');

        $orders = DB::table('customer_orders')
            ->pluck('id', 'order_number');

        $payments = [
            [
                'order' => 'CO-2026-0001',
                'receipt' => 'OR-2026-0001',
                'date' => '2026-09-03',
                'method' => 'cash',
                'amount' => 7000,
                'status' => 'paid',
            ],
            [
                'order' => 'CO-2026-0002',
                'receipt' => 'OR-2026-0002',
                'date' => '2026-09-06',
                'method' => 'credit',
                'amount' => 11400,
                'status' => 'unpaid',
            ],
            [
                'order' => 'CO-2026-0003',
                'receipt' => 'OR-2026-0003',
                'date' => '2026-09-09',
                'method' => 'gcash',
                'amount' => 5400,
                'status' => 'paid',
            ],
            [
                'order' => 'CO-2026-0004',
                'receipt' => 'OR-2026-0004',
                'date' => '2026-09-18',
                'method' => 'bank_transfer',
                'amount' => 10750,
                'status' => 'paid',
            ],
        ];

        foreach ($payments as $payment) {
            DB::table('payments')->insert([
                'customer_order_id' => $orders[$payment['order']],
                'processed_by' => $cashier,
                'receipt_number' => $payment['receipt'],
                'payment_date' => $payment['date'],
                'payment_method' => $payment['method'],
                'amount' => $payment['amount'],
                'status' => $payment['status'],
                'reference_number' => null,
                'check_number' => null,
                'bank_name' => null,
                'check_date' => null,
                'maturity_date' => null,
                'notes' => $payment['method'] === 'credit'
                    ? 'Credit transaction / unpaid receipt.'
                    : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}