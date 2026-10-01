<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['Petron Corporation', 'Sales Department', '09171234567', 'sales@petron.com', 'Davao City'],
            ['Shell Philippines', 'Industrial Sales', '09181234567', 'sales@shell.com', 'Davao City'],
            ['Caltex Philippines', 'Commercial Sales', '09191234567', 'sales@caltex.com', 'Davao City'],
            ['Mobil Philippines', 'Account Officer', '09201234567', 'sales@mobil.com', 'Davao City'],
            ['Castrol Philippines', 'Sales Representative', '09211234567', 'sales@castrol.com', 'Davao City'],
            ['Phoenix Petroleum', 'Sales Department', '09221234567', 'sales@phoenix.com', 'Davao City'],
            ['TotalEnergies Philippines', 'Sales Department', '09231234567', 'sales@totalenergies.com', 'Davao City'],
            ['Eastern Petroleum', 'Account Officer', '09241234567', 'sales@easternpetroleum.com', 'Davao City'],
            ['Mindanao Industrial Supply', 'Sales Staff', '09251234567', 'sales@mis.com', 'Davao City'],
            ['Southern Lubricants Supply', 'Sales Department', '09261234567', 'sales@southernlubricants.com', 'Davao City'],
            ['Davao Auto Supply', 'Sales Staff', '09271234567', 'sales@davaoautosupply.com', 'Davao City'],
            ['Mindanao Oil Trading', 'Sales Representative', '09281234567', 'sales@mindanaooil.com', 'Davao City'],
        ];

        foreach ($suppliers as $supplier) {
            DB::table('suppliers')->insert([
                'supplier_name' => $supplier[0],
                'contact_person' => $supplier[1],
                'contact_number' => $supplier[2],
                'email' => $supplier[3],
                'address' => $supplier[4],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}