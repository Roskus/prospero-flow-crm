<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * php artisan db:seed --class=ModuleSeeder
 */
class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('module')->upsert(['id' => 1, 'name' => 'Lead'], 'id');
        DB::table('module')->upsert(['id' => 2, 'name' => 'Customer'], 'id');
        DB::table('module')->upsert(['id' => 3, 'name' => 'Product'], 'id');
        DB::table('module')->upsert(['id' => 4, 'name' => 'Order'], 'id');
        DB::table('module')->upsert(['id' => 5, 'name' => 'Supplier'], 'id');
        DB::table('module')->upsert(['id' => 6, 'name' => 'Accounting'], 'id');
        DB::table('module')->upsert(['id' => 7, 'name' => 'User'], 'id');
        DB::table('module')->upsert(['id' => 8, 'name' => 'Company'], 'id');
        DB::table('module')->upsert(['id' => 9, 'name' => 'Report'], 'id');
    }
}
