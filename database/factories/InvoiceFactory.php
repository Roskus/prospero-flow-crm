<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'branch_id' => fn (array $attributes) => Branch::factory()->forCompany($attributes['company_id']),
            'customer_id' => fn (array $attributes) => Customer::factory()->state(['company_id' => $attributes['company_id']]),
            'series' => Branch::MAIN_CODE,
            'number' => fake()->unique()->numberBetween(1, 9999999),
            'issue_date' => today()->toDateString(),
            'due_date' => today()->toDateString(),
            'currency' => 'EUR',
            'exchange_rate' => 1,
            'subtotal' => 100,
            'tax' => 21,
            'total' => 121,
            'paid_amount' => 0,
            'status' => Invoice::ISSUED,
            'customer_data' => ['name' => fake()->name(), 'business_name' => null, 'vat' => null, 'address' => []],
            'issuer_data' => ['company' => ['business_name' => fake()->company(), 'currency' => 'EUR'], 'branch' => ['code' => Branch::MAIN_CODE, 'name' => 'Main', 'address' => []]],
        ];
    }

    public function cancelled(): static
    {
        return $this->state(['status' => Invoice::CANCELLED, 'cancelled_at' => now()]);
    }
}
