<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts')->insert([
            [
                'id' => 1,
                'email' => 'admin@hmmi.co.id',
                'password' => md5('Hmmi12345!'),
                'no_employee' => '0000000000',
                'name' => 'Admin Master',
                'position' => 'Administrator',
                'deptID' => 1, // master dept (changed from 0 to 1)
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 