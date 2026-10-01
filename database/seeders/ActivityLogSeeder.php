<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $owner = DB::table('users')
            ->where('username', 'owner')
            ->value('id');

        $secretary = DB::table('users')
            ->where('username', 'secretary')
            ->value('id');

        $cashier = DB::table('users')
            ->where('username', 'cashier')
            ->value('id');

        $admin = DB::table('users')
            ->where('username', 'admin')
            ->value('id');

        $logs = [
            [
                $owner,
                'login',
                'Authentication',
                'Owner logged into the system.',
                null,
            ],
            [
                $admin,
                'login',
                'Authentication',
                'Admin logged into the system.',
                null,
            ],
            [
                $secretary,
                'inventory_check',
                'Customer Orders',
                'Inventory availability checked for customer order CO-2026-0001.',
                'CO-2026-0001',
            ],
            [
                $owner,
                'confirm',
                'Customer Orders',
                'Customer order CO-2026-0001 was confirmed.',
                'CO-2026-0001',
            ],
            [
                $owner,
                'approve',
                'Purchase Orders',
                'Purchase order PO-2026-0002 was approved.',
                'PO-2026-0002',
            ],
            [
                $cashier,
                'create',
                'Payments',
                'Receipt OR-2026-0001 was recorded.',
                'OR-2026-0001',
            ],
            [
                $cashier,
                'create',
                'Payments',
                'Credit receipt OR-2026-0002 was recorded as unpaid.',
                'OR-2026-0002',
            ],
            [
                $secretary,
                'stock_out',
                'Inventory',
                'Customer delivery for CO-2026-0001 was recorded.',
                'OR-2026-0001',
            ],
            [
                $owner,
                'stock_in',
                'Inventory',
                'Supplier delivery was recorded into inventory.',
                'PO-2026-0002',
            ],
            [
                $secretary,
                'adjustment',
                'Inventory',
                'Physical inventory adjustment was recorded.',
                'Count Sheet - September 2026',
            ],
        ];

        foreach ($logs as $log) {
            DB::table('activity_logs')->insert([
                'user_id' => $log[0],
                'action' => $log[1],
                'module' => $log[2],
                'description' => $log[3],
                'reference' => $log[4],
                'ip_address' => '127.0.0.1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}