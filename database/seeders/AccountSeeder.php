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
            'email' => 'admin@hmmi.co.id',
            'password' => md5('Hmmi12345!'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
} 