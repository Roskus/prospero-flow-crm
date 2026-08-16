<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Module;
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
            ['id' => Module::LEAD, 'name' => 'Lead'],
            ['id' => Module::CUSTOMER, 'name' => 'Customer'],
            ['id' => Module::PRODUCT, 'name' => 'Product'],
            ['id' => Module::ORDER, 'name' => 'Order'],
            ['id' => Module::SUPPLIER, 'name' => 'Supplier'],
            ['id' => Module::ACCOUNTING, 'name' => 'Accounting'],
            ['id' => Module::USER, 'name' => 'User'],
            ['id' => Module::COMPANY, 'name' => 'Company'],
            ['id' => Module::REPORT, 'name' => 'Report'],
            ['id' => Module::TICKET, 'name' => 'Ticket'],
            ['id' => Module::HUMAN_RESOURCES, 'name' => 'RRHH'],
        ], 'id', ['name']);
    }
}
