<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Bank;

use App\Models\Bank\Account as BankAccount;
use App\Models\Company;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BankAccountDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_bank_account(): void
    {
        $bankAccount = BankAccount::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->delete('/bank-account/delete/'.$bankAccount->id);

        $response->assertRedirect('/bank-account');
        $this->assertSoftDeleted($bankAccount);
    }

    #[Test]
    public function it_returns_404_when_bank_account_not_found(): void
    {
        $response = $this->delete('/bank-account/delete/99999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_cannot_delete_bank_account_from_another_company(): void
    {
        $otherCompany = Company::factory()->create();
        $bankAccount = BankAccount::factory()->create(['company_id' => $otherCompany->id]);

        $response = $this->delete('/bank-account/delete/'.$bankAccount->id);

        $response->assertNotFound();
        $this->assertDatabaseHas('bank_account', ['id' => $bankAccount->id]);
    }
}
