<?php

declare(strict_types=1);

namespace Database\Factories\Supplier;

use App\Models\Company;
use App\Models\Supplier;
use App\Models\Supplier\SupplierContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupplierContact>
 */
class SupplierContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'supplier_id' => Supplier::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => fake()->numerify('6########'),
            'email' => fake()->email(),
        ];
    }
}
