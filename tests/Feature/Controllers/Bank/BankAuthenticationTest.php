<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Bank;

use App\Models\Bank\Account as BankAccount;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BankAuthenticationTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_access_to_bank_index(): void
    {
        auth()->guard('web')->logout();

        $response = $this->get('/bank');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_blocks_unauthenticated_access_to_bank_create(): void
    {
        auth()->guard('web')->logout();

        $response = $this->get('/bank/create');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_blocks_unauthenticated_access_to_bank_update(): void
    {
        auth()->guard('web')->logout();

        $response = $this->get('/bank/update/test-uuid-1234-5678-90ab');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_blocks_unauthenticated_access_to_bank_save(): void
    {
        auth()->guard('web')->logout();

        $response = $this->post('/bank/save', [
            'name' => 'Test Bank',
            'country_id' => 'US',
            'bic' => 'TESTBIC1234',
        ]);

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_blocks_unauthenticated_access_to_bank_account_index(): void
    {
        auth()->guard('web')->logout();

        $response = $this->get('/bank-account');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_blocks_unauthenticated_access_to_bank_account_create(): void
    {
        auth()->guard('web')->logout();

        $response = $this->get('/bank-account/create');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_blocks_unauthenticated_access_to_bank_account_update(): void
    {
        $bankAccount = BankAccount::where('company_id', $this->user->company_id)->first();

        if ($bankAccount) {
            auth()->guard('web')->logout();

            $response = $this->get("/bank-account/update/{$bankAccount->id}");

            $response->assertRedirect(route('login'));
        }
    }

    #[Test]
    public function it_blocks_unauthenticated_access_to_bank_account_save(): void
    {
        auth()->guard('web')->logout();

        $response = $this->post('/bank-account/save', [
            'type' => 'bank',
            'country_id' => 'US',
            'currency' => 'USD',
        ]);

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_allows_authenticated_access_to_bank_index(): void
    {
        $response = $this->get('/bank');

        $response->assertOk();
    }

    #[Test]
    public function it_allows_authenticated_access_to_bank_create(): void
    {
        $response = $this->get('/bank/create');

        $response->assertOk();
    }
}
