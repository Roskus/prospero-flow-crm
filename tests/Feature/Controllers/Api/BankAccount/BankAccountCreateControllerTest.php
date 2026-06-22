<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\BankAccount;

use App\Models\Bank;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BankAccountCreateControllerTest extends TestCase
{
    #[Test]
    public function it_allows_user_with_create_accounting_permission_to_create_bank_account(): void
    {
        $this->actingAs($this->user, 'api');
        $bank = Bank::factory()->create();
        $bankId = DB::table('bank')->where('uuid', $bank->uuid)->value('id');

        $response = $this->postJson('/api/bank-account', [
            'bank_id' => $bankId,
            'country_id' => 'ES',
            'currency' => 'EUR',
            'iban' => 'ES9121000418450200051332',
            'opening_balance' => 1000.00,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['bank_account' => ['id']]);
    }

    #[Test]
    public function it_denies_user_without_permission_to_create_bank_account(): void
    {
        $userWithoutPermission = User::factory()->create();
        $bank = Bank::factory()->create();
        $bankId = DB::table('bank')->where('uuid', $bank->uuid)->value('id');

        $this->actingAs($userWithoutPermission, 'api');

        $response = $this->postJson('/api/bank-account', [
            'bank_id' => $bankId,
            'country_id' => 'ES',
            'currency' => 'EUR',
            'iban' => 'ES9121000418450200051332',
            'opening_balance' => 1000.00,
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function it_requires_authentication_to_create_bank_account(): void
    {
        $bank = Bank::factory()->create();
        $bankId = DB::table('bank')->where('uuid', $bank->uuid)->value('id');

        $response = $this->postJson('/api/bank-account', [
            'bank_id' => $bankId,
            'country_id' => 'ES',
            'currency' => 'EUR',
            'iban' => 'ES9121000418450200051332',
            'opening_balance' => 1000.00,
        ]);

        $response->assertUnauthorized();
    }
}
