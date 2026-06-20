<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Transaction;

use App\Models\Transaction;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_transaction(): void
    {
        $this->actingAs($this->user, 'api');

        $transaction = Transaction::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->deleteJson('/api/transaction/'.$transaction->id);

        $response->assertOk();
        $this->assertSoftDeleted($transaction);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_transaction(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->deleteJson('/api/transaction/99999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $transaction = Transaction::factory()->create(['company_id' => 999]);

        $response = $this->deleteJson('/api/transaction/'.$transaction->id);

        $response->assertUnauthorized();
    }
}
