<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Transaction\Category;
use Illuminate\Database\Seeder;

class TransactionCategorySeeder extends Seeder
{
    /**
     * Default accounting categories seeded for every company.
     * Names are stored in English and translated via i18n.
     * php artisan db:seed --class=TransactionCategorySeeder
     */
    public function run(): void
    {
        $categories = [
            'Sales',
            'Services',
            'Rent',
            'Taxes',
            'Marketing',
            'Travel',
            'Salaries',
            'Utilities',
            'Office supplies',
            'Other',
        ];

        Company::all()->each(function (Company $company) use ($categories) {
            foreach ($categories as $name) {
                Category::firstOrCreate([
                    'company_id' => $company->id,
                    'name' => $name,
                ]);
            }
        });
    }
}
