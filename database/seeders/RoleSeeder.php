<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * php artisan db:seed --class=RoleSeeder
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role')->insert(['name' => 'SuperAdmin', 'guard_name' => 'web', 'created_at' => now(),]);
        DB::table('role')->insert(['name' => 'CompanyAdmin', 'guard_name' => 'web', 'created_at' => now(),]);
        DB::table('role')->insert(['name' => 'Seller', 'guard_name' => 'web', 'created_at' => now(),]);
        DB::table('role')->insert(['name' => 'Buyer', 'guard_name' => 'web', 'created_at' => now(),]);
        DB::table('role')->insert(['name' => 'User', 'guard_name' => 'web', 'created_at' => now(),]);
    }
}
