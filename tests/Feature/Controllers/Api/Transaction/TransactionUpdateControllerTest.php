<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Transaction;

use App\Models\Transaction;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionUpdateControllerTest extends TestCase
{
    #[Test]
    public function it_can_update_transaction(): void
    {
        $this->actingAs($this->user, 'api');

        $transaction = Transaction::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->putJson('/api/transaction/'.$transaction->id, [
            'name' => 'Updated Transaction',
            'amount' => 2000.75,
            'status' => 'paid',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['transaction']);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_transaction(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->putJson('/api/transaction/99999', [
            'name' => 'Updated',
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $transaction = Transaction::factory()->create(['company_id' => 999]);

        $response = $this->putJson('/api/transaction/'.$transaction->id, [
            'name' => 'Updated',
        ]);

        $response->assertUnauthorized();
    }
}
