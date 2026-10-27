<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Bank;

use App\Models\Bank\Account;
use App\Models\Branch;
use App\Models\Company;
use App\Services\BranchService;
use App\Services\CashAccountService;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CashAccountTest extends TestCase
{
    #[Test]
    public function a_new_branch_gets_a_cash_account_in_the_company_currency(): void
    {
        $company = Company::factory()->create(['currency' => 'ARS', 'country_id' => 'ar']);

        $branch = Branch::factory()->forCompany($company)->create(['name' => 'Centro']);

        $account = $this->cashAccountOf($branch);
        $this->assertSame('Cash Centro', $account->account_name);
        $this->assertSame('ARS', $account->currency);
        $this->assertSame('ar', $account->country_id);
        $this->assertSame($company->id, $account->company_id);
        $this->assertNotNull($this->cashAccountOf($company->mainBranch));
    }

    #[Test]
    public function creating_the_cash_account_twice_returns_the_same_account(): void
    {
        $branch = Branch::factory()->forCompany($this->user->company_id)->create();

        $again = app(CashAccountService::class)->createForBranch($branch);

        $this->assertTrue($again->is($this->cashAccountOf($branch)));
        $this->assertSame(1, Account::where('branch_id', $branch->id)->count());
    }

    #[Test]
    public function the_migration_gives_every_existing_branch_one_cash_account(): void
    {
        $branch = Branch::withoutEvents(fn () => Branch::factory()->forCompany($this->user->company_id)->create());
        $withAccount = Branch::factory()->forCompany($this->user->company_id)->create();

        $migration = require glob(database_path('migrations/*_create_cash_account_for_every_branch.php'))[0];
        $migration->up();
        $migration->up();

        $this->assertSame(1, Account::where('branch_id', $branch->id)->where('type', Account::TYPE_CASH)->count());
        $this->assertSame(1, Account::where('branch_id', $withAccount->id)->count());
        $this->assertSame(0, (int) DB::table('branch')->whereNotExists(fn ($query) => $query->select(DB::raw(1))->from('bank_account')->whereColumn('bank_account.branch_id', 'branch.id'))->count());
    }

    #[Test]
    public function a_cash_account_only_needs_a_name_and_a_currency(): void
    {
        $this->post('/bank-account/save', ['type' => 'cash', 'account_name' => 'Caja chica', 'currency' => 'EUR', 'show_on_invoice' => 1])
            ->assertRedirect('/bank-account');

        $this->assertDatabaseHas('bank_account', [
            'company_id' => $this->user->company_id,
            'type' => 'cash',
            'account_name' => 'Caja chica',
            'show_on_invoice' => false,
        ]);
        $this->post('/bank-account/save', ['type' => 'cash', 'currency' => 'EUR'])->assertSessionHasErrors('account_name');
    }

    #[Test]
    public function a_bank_account_can_be_shown_on_invoices(): void
    {
        $account = Account::factory()->create(['company_id' => $this->user->company_id, 'country_id' => 'es', 'currency' => 'EUR']);

        $this->post('/bank-account/save', $this->bankPayload($account) + ['show_on_invoice' => 1])->assertRedirect('/bank-account');
        $this->assertTrue($account->fresh()->show_on_invoice);

        $this->post('/bank-account/save', $this->bankPayload($account))->assertRedirect('/bank-account');
        $this->assertFalse($account->fresh()->show_on_invoice);

        $this->get('/bank-account/update/'.$account->id)->assertOk()->assertSee('name="show_on_invoice"', false);
    }

    #[Test]
    public function the_cash_account_of_a_branch_cannot_be_deleted_or_retyped(): void
    {
        $account = $this->cashAccountOf($this->user->company->mainBranch);

        $this->delete('/bank-account/delete/'.$account->id)->assertRedirect('/bank-account')->assertSessionHas('status', 'error');
        $this->assertNotSoftDeleted('bank_account', ['id' => $account->id]);

        $this->post('/bank-account/save', ['id' => $account->id, 'type' => 'bank', 'account_name' => 'Renamed', 'currency' => 'EUR', 'country_id' => 'es'])
            ->assertRedirect('/bank-account');
        $this->assertSame('cash', $account->fresh()->type);
        $this->assertSame('Renamed', $account->fresh()->account_name);

        $this->actingAs($this->user, 'api')->deleteJson('/api/bank-account/'.$account->id)->assertConflict();
        $this->assertNotSoftDeleted('bank_account', ['id' => $account->id]);
    }

    #[Test]
    public function the_cash_account_of_a_deleted_branch_can_be_deleted(): void
    {
        $branch = Branch::factory()->forCompany($this->user->company_id)->create();
        $account = $this->cashAccountOf($branch);
        app(BranchService::class)->delete($branch);

        $this->delete('/bank-account/delete/'.$account->id)->assertRedirect('/bank-account');

        $this->assertSoftDeleted('bank_account', ['id' => $account->id]);
    }

    private function cashAccountOf(Branch $branch): Account
    {
        return Account::where('branch_id', $branch->id)->where('type', Account::TYPE_CASH)->firstOrFail();
    }

    /**
     * @return array<string, mixed>
     */
    private function bankPayload(Account $account): array
    {
        return ['id' => $account->id, 'type' => 'bank', 'currency' => 'EUR', 'country_id' => 'es', 'iban' => $account->iban];
    }
}
