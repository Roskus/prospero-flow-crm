<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Bank;

use App\Models\Bank\Account;
use App\Models\BankCard;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BankCardCvvTest extends TestCase
{
    #[Test]
    public function the_card_security_code_is_not_stored(): void
    {
        $this->assertFalse(Schema::hasColumn('bank_card', 'cvv_encrypted'));
    }

    #[Test]
    public function the_web_card_form_has_no_cvv_and_ignores_one_sent(): void
    {
        $account = $this->account();

        $this->get('/bank-card/create/'.$account->id)->assertOk()->assertDontSee('name="cvv"', false);

        $this->post('/bank-card/save', $this->cardPayload($account) + ['cvv' => '123'])
            ->assertRedirect('/bank-account/show/'.$account->id);

        $card = BankCard::where('bank_account_id', $account->id)->firstOrFail();
        $this->assertArrayNotHasKey('cvv', $card->getAttributes());
        $this->get('/bank-account/show/'.$account->id)->assertOk()->assertDontSee('CVV');
    }

    #[Test]
    public function the_card_api_ignores_a_cvv_and_never_returns_one(): void
    {
        $this->actingAs($this->user, 'api');
        $account = $this->account();

        $id = $this->postJson('/api/bank-card', $this->cardPayload($account) + ['cvv' => '123'])->assertCreated()->json('bank_card.id');

        $this->getJson('/api/bank-card/'.$id)->assertOk()->assertJsonMissingPath('bank_card.cvv');
        $this->assertStringNotContainsString('cvv', strtolower((string) json_encode(BankCard::findOrFail($id)->toArray())));
    }

    private function account(): Account
    {
        return Account::factory()->create(['company_id' => $this->user->company_id]);
    }

    /**
     * @return array<string, mixed>
     */
    private function cardPayload(Account $account): array
    {
        return [
            'bank_account_id' => $account->id,
            'type' => 'credit',
            'network' => 'visa',
            'cardholder_name' => 'Ana Pérez',
            'number' => '4111111111111111',
            'expires_month' => 12,
            'expires_year' => (int) date('Y') + 2,
        ];
    }
}
