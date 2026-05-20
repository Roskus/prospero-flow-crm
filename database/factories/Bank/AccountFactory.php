<?php

declare(strict_types=1);

namespace Database\Factories\Bank;

use App\Models\Bank;
use App\Models\Bank\Account;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Squire\Models\Country;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'bank_id' => function () {
                $bank = Bank::factory()->create();

                return DB::table('bank')->where('uuid', $bank->getKey())->value('id');
            },
            'country_id' => Country::all()->random(),
            'currency' => fake()->currencyCode(),
            'iban' => fake()->iban(),
            'amount' => fake()->randomFloat(3, 0, 100000),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
