<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::table('company')->count() === 0) {
            DB::table('company')->insert(['country' => 'ee', 'name' => 'Test company', 'email' => 'info@test.com', 'website' => 'https://test.com']);
        }
    }
}
