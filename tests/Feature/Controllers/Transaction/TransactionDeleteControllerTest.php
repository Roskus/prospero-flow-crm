<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Transaction;

use App\Models\Transaction;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_transaction(): void
    {
        $transaction = Transaction::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->delete('/transaction/delete/'.$transaction->id);

        $response->assertRedirect();
        $this->assertSoftDeleted($transaction);
    }

    #[Test]
    public function it_blocks_user_without_permission(): void
    {
        $transaction = Transaction::factory()->create(['company_id' => $this->user->company_id]);
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->delete('/transaction/delete/'.$transaction->id);

        $response->assertForbidden();
    }

    #[Test]
    public function it_returns_404_for_transaction_from_another_company(): void
    {
        $otherUser = User::factory()->create();
        $transaction = Transaction::factory()->create(['company_id' => $otherUser->company_id]);

        $response = $this->delete('/transaction/delete/'.$transaction->id);

        $response->assertRedirect();
        $this->assertDatabaseHas('transaction', ['id' => $transaction->id]);
    }
}
