<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\BankAccount;

use App\Models\Bank\Account;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BankAccountReadControllerTest extends TestCase
{
    #[Test]
    public function it_allows_user_with_read_accounting_permission_to_read_bank_account(): void
    {
        $this->actingAs($this->user, 'api');
        $account = Account::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->getJson('/api/bank-account/'.$account->id);

        $response->assertOk();
        $response->assertJsonPath('bank_account.id', $account->id);
    }

    #[Test]
    public function it_denies_user_without_permission_to_read_bank_account(): void
    {
        $userWithoutPermission = User::factory()->create();
        $account = Account::factory()->create(['company_id' => $userWithoutPermission->company_id]);

        $this->actingAs($userWithoutPermission, 'api');

        $response = $this->getJson('/api/bank-account/'.$account->id);

        $response->assertStatus(403);
    }

    #[Test]
    public function it_requires_authentication_to_read_bank_account(): void
    {
        $account = Account::factory()->create();

        $response = $this->getJson('/api/bank-account/'.$account->id);

        $response->assertUnauthorized();
    }
}
