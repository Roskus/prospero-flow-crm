<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Company::count() === 0) {
            Company::create(['country_id' => 'ee', 'name' => 'Test company', 'email' => 'info@test.com', 'website' => 'https://test.com', 'status' => Company::ACTIVE]);
        }
    }
}
