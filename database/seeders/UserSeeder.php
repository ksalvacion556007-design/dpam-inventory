<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'DPAM Owner',
                'username' => 'owner',
                'email' => 'owner@dpam.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'status' => 'active',
            ],
            [
                'name' => 'DPAM Admin',
                'username' => 'admin',
                'email' => 'admin@dpam.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ],
            [
                'name' => 'DPAM Secretary',
                'username' => 'secretary',
                'email' => 'secretary@dpam.com',
                'password' => Hash::make('password'),
                'role' => 'secretary',
                'status' => 'active',
            ],
            [
                'name' => 'DPAM Cashier',
                'username' => 'cashier',
                'email' => 'cashier@dpam.com',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'status' => 'active',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert([
                ...$user,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}