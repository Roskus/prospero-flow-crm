<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * php artisan db:seed --class=RoleSeeder
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role')->upsert(['name' => 'SuperAdmin', 'guard_name' => 'web', 'created_at' => now()], "name");
        DB::table('role')->upsert(['name' => 'CompanyAdmin', 'guard_name' => 'web', 'created_at' => now()], "name");
        DB::table('role')->upsert(['name' => 'Seller', 'guard_name' => 'web', 'created_at' => now()], "name");
        DB::table('role')->upsert(['name' => 'Buyer', 'guard_name' => 'web', 'created_at' => now()], "name");
        DB::table('role')->upsert(['name' => 'User', 'guard_name' => 'web', 'created_at' => now()], "name");
        DB::table('role')->upsert(['name' => 'Support', 'guard_name' => 'web', 'created_at' => now()],"name");
    }
}
