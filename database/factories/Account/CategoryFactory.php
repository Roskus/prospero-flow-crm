<?php

declare(strict_types=1);

namespace Database\Factories\Account;

use App\Models\Account\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'company_id' => 1,
            'name' => fake()->randomElement([
                'Sales', 'Services', 'Taxes', 'Salaries', 'Rent',
                'Utilities', 'Marketing', 'Office Supplies', 'Travel', 'Other',
            ]),
        ];
    }
}
