<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\BankAccount;

use App\Models\Bank\Account;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BankAccountListControllerTest extends TestCase
{
    #[Test]
    public function it_allows_user_with_read_accounting_permission_to_list_bank_accounts(): void
    {
        $this->actingAs($this->user, 'api');
        Account::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->getJson('/api/bank-account');

        $response->assertOk();
        $response->assertJsonCount(1, 'bank_accounts');
    }

    #[Test]
    public function it_denies_user_without_permission_to_list_bank_accounts(): void
    {
        $userWithoutPermission = User::factory()->create();

        $this->actingAs($userWithoutPermission, 'api');

        $response = $this->getJson('/api/bank-account');

        $response->assertStatus(403);
    }

    #[Test]
    public function it_requires_authentication_to_list_bank_accounts(): void
    {
        $response = $this->getJson('/api/bank-account');

        $response->assertUnauthorized();
    }
}
