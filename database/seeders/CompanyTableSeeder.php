<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('company')->insert(
            [
                'name' => 'Test company',
                'country_id' => 'ES',
                'email' => 'admin@admin.com',
                'website' => 'http://www.admin.com',
                'status' => Company::ACTIVE,
            ]
        );
    }
}
