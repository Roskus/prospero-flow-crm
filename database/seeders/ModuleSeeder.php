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
        DB::table('module')->upsert([
            ['name' => 'Lead'],
            ['name' => 'Customer'],
            ['name' => 'Product'],
            ['name' => 'Order'],
            ['name' => 'Supplier'],
            ['name' => 'Accounting'],
            ['name' => 'User'],
            ['name' => 'Company'],
            ['name' => 'Report'],
        ], ['name'], ['name']);
    }
}
