<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Petron Corporation',
                'contact_person' => 'Sales Department',
                'contact_number' => '09171234567',
                'email' => 'sales@petron.com',
                'address' => 'Davao City',
                'status' => 'active',
            ],
            [
                'name' => 'Shell Philippines',
                'contact_person' => 'Industrial Sales',
                'contact_number' => '09181234567',
                'email' => 'sales@shell.com',
                'address' => 'Davao City',
                'status' => 'active',
            ],
            [
                'name' => 'Caltex Philippines',
                'contact_person' => 'Commercial Sales',
                'contact_number' => '09191234567',
                'email' => 'sales@caltex.com',
                'address' => 'Davao City',
                'status' => 'active',
            ],
            [
                'name' => 'Mobil Philippines',
                'contact_person' => 'Account Officer',
                'contact_number' => '09201234567',
                'email' => 'sales@mobil.com',
                'address' => 'Davao City',
                'status' => 'active',
            ],
            [
                'name' => 'Castrol Philippines',
                'contact_person' => 'Sales Representative',
                'contact_number' => '09211234567',
                'email' => 'sales@castrol.com',
                'address' => 'Davao City',
                'status' => 'active',
            ],
            [
                'name' => 'Phoenix Petroleum',
                'contact_person' => 'Sales Department',
                'contact_number' => '09221234567',
                'email' => 'sales@phoenix.com',
                'address' => 'Davao City',
                'status' => 'active',
            ],
            [
                'name' => 'TotalEnergies Philippines',
                'contact_person' => 'Sales Department',
                'contact_number' => '09231234567',
                'email' => 'sales@totalenergies.com',
                'address' => 'Davao City',
                'status' => 'active',
            ],
            [
                'name' => 'Eastern Petroleum',
                'contact_person' => 'Account Officer',
                'contact_number' => '09241234567',
                'email' => 'sales@easternpetroleum.com',
                'address' => 'Davao City',
                'status' => 'inactive',
            ],
            [
                'name' => 'Mindanao Industrial Supply',
                'contact_person' => 'Sales Staff',
                'contact_number' => '09251234567',
                'email' => 'sales@mis.com',
                'address' => 'Davao City',
                'status' => 'active',
            ],
            [
                'name' => 'Southern Lubricants Supply',
                'contact_person' => 'Sales Department',
                'contact_number' => '09261234567',
                'email' => 'sales@southernlubricants.com',
                'address' => 'Davao City',
                'status' => 'active',
            ],
            [
                'name' => 'Davao Auto Supply',
                'contact_person' => 'Sales Staff',
                'contact_number' => '09271234567',
                'email' => 'sales@davaoautosupply.com',
                'address' => 'Davao City',
                'status' => 'active',
            ],
            [
                'name' => 'Mindanao Oil Trading',
                'contact_person' => 'Sales Representative',
                'contact_number' => '09281234567',
                'email' => 'sales@mindanaooil.com',
                'address' => 'Davao City',
                'status' => 'archived',
            ],
        ];

        foreach ($suppliers as $supplier) {
            DB::table('suppliers')->insert([
                'supplier_name' => $supplier['name'],
                'contact_person' => $supplier['contact_person'],
                'contact_number' => $supplier['contact_number'],
                'email' => $supplier['email'],
                'address' => $supplier['address'],
                'status' => $supplier['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}