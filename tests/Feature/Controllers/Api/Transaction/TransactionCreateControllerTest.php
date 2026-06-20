<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Transaction;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionCreateControllerTest extends TestCase
{
    #[Test]
    public function it_can_create_transaction(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->postJson('/api/transaction', [
            'issue_date' => '2025-06-19',
            'name' => 'Test Transaction',
            'type' => 'income',
            'amount' => 1000.50,
            'status' => 'pending',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure(['transaction']);
    }

    #[Test]
    public function it_validates_required_fields(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->postJson('/api/transaction', []);

        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors']);
    }

    #[Test]
    public function it_validates_type_enum(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->postJson('/api/transaction', [
            'issue_date' => '2025-06-19',
            'name' => 'Test',
            'type' => 'invalid_type',
            'amount' => 100,
        ]);

        $response->assertUnprocessable();
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $response = $this->postJson('/api/transaction', [
            'issue_date' => '2025-06-19',
            'name' => 'Test',
            'type' => 'income',
            'amount' => 100,
        ]);

        $response->assertUnauthorized();
    }
}
