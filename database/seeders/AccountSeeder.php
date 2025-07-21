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
        $departments = DB::table('depts')->get();
        $accounts = [];
        $now = now();
        foreach ($departments as $dept) {
            $email = strtolower(str_replace(' ', '_', $dept->nameDept)) . '@hmmi.co.id';
            $accounts[] = [
                'email' => $email,
                'password' => md5('Hmmi12345!'),
                'no_employee' => str_pad($dept->deptID, 10, '0', STR_PAD_LEFT),
                'name' => ucfirst($dept->nameDept) . ' Admin',
                'position' => 'Department Admin',
                'deptID' => $dept->deptID,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('accounts')->insert($accounts);
    }
} 