<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Ticket;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
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
        $user = $this->createTicketCreator();
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
        $user = $this->createTicketCreator();
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
    public function it_forbids_users_without_create_ticket_permission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/ticket', $this->validPayload($user));

        $response->assertForbidden();
        $this->assertDatabaseMissing('ticket', ['title' => 'Test Ticket']);
    }

    #[Test]
    public function it_rejects_customer_and_order_from_another_company(): void
    {
        $user = $this->createTicketCreator();
        $this->actingAs($user, 'api');
        $payload = $this->validPayload(User::factory()->create());

        $response = $this->postJson('/api/ticket', $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['customer_id', 'order_id']);
    }

    #[Test]
    public function it_ignores_created_by_from_the_payload(): void
    {
        $user = $this->createTicketCreator();
        $this->actingAs($user, 'api');
        $payload = $this->validPayload($user);
        $payload['created_by'] = $this->user->id;

        $response = $this->postJson('/api/ticket', $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('ticket', ['id' => $response->json('ticket.id'), 'created_by' => $user->id]);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $response = $this->postJson('/api/ticket', []);

        $response->assertUnauthorized();
    }

    private function createTicketCreator(): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('create ticket', 'web'));

        return $user;
    }
}
