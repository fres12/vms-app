<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('depts')->insert([
            [
                'deptID' => 1,
                'nameDept' => 'master',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 2,
                'nameDept' => 'Accounting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 3,
                'nameDept' => 'Assembly',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 4,
                'nameDept' => 'Body',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 5,
                'nameDept' => 'Business Supporting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 6,
                'nameDept' => 'Costing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 7,
                'nameDept' => 'Cost Analyst',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 8,
                'nameDept' => 'Employee Relation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 9,
                'nameDept' => 'Engine',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 10,
                'nameDept' => 'External Affair',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 11,
                'nameDept' => 'Facility Management',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 12,
                'nameDept' => 'General Purchasing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 13,
                'nameDept' => 'Human Resource',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 14,
                'nameDept' => 'Maintenance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 15,
                'nameDept' => 'Paint',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 16,
                'nameDept' => 'Part Development 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 17,
                'nameDept' => 'Part Development 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 18,
                'nameDept' => 'Press',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 19,
                'nameDept' => 'Production Control',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 20,
                'nameDept' => 'Project',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 21,
                'nameDept' => 'Quality Assurance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 22,
                'nameDept' => 'Quality Center',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 23,
                'nameDept' => 'Quality Control',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 24,
                'nameDept' => 'R&D',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 25,
                'nameDept' => 'Sales Support',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'deptID' => 26,
                'nameDept' => 'SHE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 