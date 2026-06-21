<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Ticket;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TicketCreateControllerTest extends TestCase
{
    private function validPayload(User $user): array
    {
        $customer = Customer::factory()->create(['company_id' => $user->company_id, 'seller_id' => $user->id]);
        $order = Order::factory()->create(['company_id' => $user->company_id, 'customer_id' => $customer->id, 'seller_id' => $user->id]);

        return [
            'title' => 'Test Ticket',
            'description' => 'Test description',
            'customer_id' => $customer->id,
            'type' => 'support',
            'priority' => 'high',
            'order_id' => $order->id,
            'status' => 'new',
        ];
    }

    #[Test]
    public function it_can_create_a_ticket(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/ticket', $this->validPayload($user));

        $response->assertCreated();
        $this->assertDatabaseHas('ticket', [
            'title' => 'Test Ticket',
            'company_id' => $user->company_id,
            'created_by' => $user->id,
        ]);
    }

    #[Test]
    public function it_forces_company_id_from_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $payload = $this->validPayload($user);
        $payload['company_id'] = 99999;

        $response = $this->postJson('/api/ticket', $payload);

        $response->assertCreated();
        $ticketId = $response->json('ticket.id');
        $this->assertDatabaseHas('ticket', ['id' => $ticketId, 'company_id' => $user->company_id]);
    }

    #[Test]
    public function it_validates_required_fields(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->postJson('/api/ticket', []);

        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors']);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $response = $this->postJson('/api/ticket', []);

        $response->assertUnauthorized();
    }
}
