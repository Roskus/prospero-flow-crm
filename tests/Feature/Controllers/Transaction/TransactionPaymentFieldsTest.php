<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Transaction;

use App\Models\Bank\Account;
use App\Models\Company;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionPaymentFieldsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user->company->update(['currency' => 'EUR']);
    }

    #[Test]
    public function an_expense_without_invoice_takes_the_currency_of_its_account(): void
    {
        $this->user->company->currencyRates()->create(['currency' => 'USD', 'conversion_rate' => 0.9]);
        $account = Account::factory()->create(['company_id' => $this->user->company_id, 'currency' => 'usd']);

        $this->post('/transaction/save', [
            'name' => 'Hosting',
            'type' => Transaction::EXPENSE,
            'amount' => 50,
            'issue_date' => '2026-10-28',
            'status' => Transaction::PAID,
            'bank_account_id' => $account->id,
        ])->assertRedirect('accounting');

        $transaction = Transaction::sole();
        $this->assertSame('USD', $transaction->currency);
        $this->assertSame('0.900000', $transaction->exchange_rate);
        $this->assertNull($transaction->payment_method);
        $this->assertNull($transaction->invoice_id);
    }

    #[Test]
    public function a_transaction_without_account_takes_the_company_currency(): void
    {
        $this->post('/transaction/save', [
            'name' => 'Office',
            'type' => Transaction::EXPENSE,
            'amount' => 20,
            'issue_date' => '2026-10-28',
            'status' => Transaction::PAID,
            'payment_method' => Transaction::METHOD_CARD,
        ])->assertRedirect('accounting');

        $transaction = Transaction::sole();
        $this->assertSame('EUR', $transaction->currency);
        $this->assertSame('1.000000', $transaction->exchange_rate);
        $this->assertSame(Transaction::METHOD_CARD, $transaction->payment_method);
    }

    #[Test]
    public function a_currency_without_rate_is_rejected(): void
    {
        $this->post('/transaction/save', [
            'name' => 'Office',
            'type' => Transaction::EXPENSE,
            'amount' => 20,
            'issue_date' => '2026-10-28',
            'status' => Transaction::PAID,
            'currency' => 'JPY',
        ])->assertSessionHasErrors('currency');

        $this->assertSame(0, Transaction::count());
    }

    #[Test]
    public function an_unknown_payment_method_is_rejected(): void
    {
        $this->post('/transaction/save', [
            'name' => 'Office',
            'type' => Transaction::EXPENSE,
            'amount' => 20,
            'issue_date' => '2026-10-28',
            'status' => Transaction::PAID,
            'payment_method' => 'bitcoin',
        ])->assertSessionHasErrors('payment_method');
    }

    #[Test]
    public function the_form_shows_the_payment_fields(): void
    {
        $this->get('/transaction/create')
            ->assertOk()
            ->assertSee('name="payment_method"', false)
            ->assertSee('name="currency"', false)
            ->assertSee('name="exchange_rate"', false);
    }

    #[Test]
    public function the_api_accepts_and_returns_the_payment_fields(): void
    {
        $this->actingAs($this->user, 'api');
        $this->user->company->currencyRates()->create(['currency' => 'USD', 'conversion_rate' => 0.9]);

        $response = $this->postJson('/api/transaction', [
            'issue_date' => '2026-10-28',
            'name' => 'Sale',
            'type' => Transaction::INCOME,
            'amount' => 100,
            'status' => Transaction::PAID,
            'payment_method' => Transaction::METHOD_TRANSFER,
            'currency' => 'usd',
            'exchange_rate' => 0.95,
        ]);

        $response->assertCreated()
            ->assertJsonPath('transaction.payment_method', Transaction::METHOD_TRANSFER)
            ->assertJsonPath('transaction.currency', 'USD')
            ->assertJsonPath('transaction.exchange_rate', '0.950000');

        $this->getJson('/api/transaction/'.$response->json('transaction.id'))
            ->assertOk()
            ->assertJsonFragment(['payment_method' => Transaction::METHOD_TRANSFER, 'currency' => 'USD']);
    }

    #[Test]
    public function the_api_rejects_an_account_of_another_company(): void
    {
        $this->actingAs($this->user, 'api');
        $foreign = Account::factory()->create(['company_id' => Company::factory()->create()->id]);

        $this->postJson('/api/transaction', [
            'issue_date' => '2026-10-28',
            'name' => 'Sale',
            'type' => Transaction::INCOME,
            'amount' => 100,
            'bank_account_id' => $foreign->id,
        ])->assertUnprocessable()->assertJsonValidationErrors('bank_account_id');
    }

    #[Test]
    public function the_backfill_sets_the_account_or_company_currency(): void
    {
        $account = Account::factory()->create(['company_id' => $this->user->company_id, 'currency' => 'usd']);
        $base = ['company_id' => $this->user->company_id, 'issue_date' => '2026-10-01', 'name' => 'Old', 'type' => 'income', 'amount' => 10, 'status' => 'paid', 'exchange_rate' => 1];
        $withAccount = DB::table('transaction')->insertGetId($base + ['bank_account_id' => $account->id]);
        $withoutAccount = DB::table('transaction')->insertGetId($base);

        (require database_path('migrations/2026_10_28_002311_backfill_transaction_currency.php'))->up();

        $this->assertSame('USD', DB::table('transaction')->where('id', $withAccount)->value('currency'));
        $this->assertSame('EUR', DB::table('transaction')->where('id', $withoutAccount)->value('currency'));
    }

    #[Test]
    public function the_account_balance_adds_paid_income_and_subtracts_paid_expenses(): void
    {
        $account = Account::factory()->create(['company_id' => $this->user->company_id, 'currency' => 'EUR', 'opening_balance' => 100]);
        $attributes = ['company_id' => $this->user->company_id, 'bank_account_id' => $account->id, 'status' => Transaction::PAID];
        Transaction::factory()->create($attributes + ['type' => Transaction::INCOME, 'amount' => 50]);
        Transaction::factory()->create($attributes + ['type' => Transaction::EXPENSE, 'amount' => 30]);
        Transaction::factory()->create(['status' => Transaction::PENDING, 'type' => Transaction::INCOME, 'amount' => 999] + $attributes);

        $this->assertSame(120.0, $account->balance());
    }
}
