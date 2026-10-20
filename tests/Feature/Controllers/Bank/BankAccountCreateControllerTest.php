<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Bank;

use App\Models\Bank;
use App\Models\Bank\Account as BankAccount;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BankAccountCreateControllerTest extends TestCase
{
    #[Test]
    public function the_bank_is_chosen_with_the_searchable_bank_component(): void
    {
        $bank = Bank::factory()->create(['name' => 'Searchable bank', 'bic' => 'SRCHESMMXXX'])->fresh();

        $this->get('/bank-account/create')
            ->assertOk()
            ->assertSee('<select name="bank_id" id="bank_id" class="form-select form-select-lg" data-tom-select', false)
            ->assertSee('<option value="'.$bank->id.'" data-bic="SRCHESMMXXX"', false)
            ->assertSee('Searchable bank')
            ->assertDontSee('id="bank_search"', false);
    }

    #[Test]
    public function the_banks_are_filtered_by_the_country_of_the_bank_account(): void
    {
        $spanishBank = Bank::factory()->create(['country_id' => 'es'])->fresh();
        $bankAccount = BankAccount::factory()->create(['company_id' => $this->user->company_id, 'country_id' => 'ES', 'bank_id' => $spanishBank->id]);

        $this->get('/bank-account/update/'.$bankAccount->id)
            ->assertOk()
            ->assertSee('data-bank-country="es"', false)
            ->assertSee('data-country="es"', false)
            ->assertSee('function filterBanksByCountry', false);
    }

    #[Test]
    public function the_banks_are_not_filtered_without_country(): void
    {
        $this->get('/bank-account/create')
            ->assertOk()
            ->assertSee('data-bank-country=""', false);
    }

    #[Test]
    public function editing_a_bank_account_selects_its_bank(): void
    {
        $bank = Bank::factory()->create()->fresh();
        $bankAccount = BankAccount::factory()->create(['company_id' => $this->user->company_id, 'bank_id' => $bank->id]);

        $this->get('/bank-account/update/'.$bankAccount->id)
            ->assertOk()
            ->assertSee('<option value="'.$bank->id.'" data-bic="'.$bank->bic.'" data-country="'.$bank->country_id.'" selected>', false);
    }

    #[Test]
    public function the_currency_is_chosen_with_the_searchable_currency_component(): void
    {
        $bankAccount = BankAccount::factory()->create(['company_id' => $this->user->company_id, 'currency' => 'ARS']);

        $response = $this->get('/bank-account/update/'.$bankAccount->id);

        $response->assertOk();
        $this->assertMatchesRegularExpression('/<select name="currency" id="currency" class="form-select form-select-lg" data-tom-select[^>]*required/', $response->getContent());
        $this->assertMatchesRegularExpression('/<option value="ARS"\s+selected>ARS - /', $response->getContent());
    }
}
