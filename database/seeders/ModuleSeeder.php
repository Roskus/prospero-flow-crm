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
    const int INVENTORY_MODULE_ID = 15;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('module')->upsert([
            ['name' => 'Lead'], // 1
            ['name' => 'Customer'], // 2
            ['name' => 'Product'], // 3
            ['name' => 'Order'], // 4
            ['name' => 'Supplier'], // 5
            ['name' => 'Accounting'], // 6
            ['name' => 'User'], // 7
            ['name' => 'Company'], // 8
            ['name' => 'Report'], // 9
        ], ['name'], ['name']);

        DB::table('module')->upsert([
            ['id' => self::INVENTORY_MODULE_ID, 'name' => 'Inventory'],
        ], ['id'], ['name']);
    }
}
