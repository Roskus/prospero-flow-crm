<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Ticket;

use App\Models\Ticket;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TicketCreateMessageControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_message_save(): void
    {
        auth()->guard('web')->logout();

        $response = $this->post('/ticket/message/save', []);

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_can_save_ticket_message(): void
    {
        $ticket = Ticket::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/ticket/message/save', [
            'ticket_id' => $ticket->id,
            'message' => 'Test message',
        ]);

        $response->assertRedirect("ticket/update/{$ticket->id}");
        $this->assertDatabaseHas('ticket_message', [
            'ticket_id' => $ticket->id,
            'author_id' => $this->user->id,
            'body' => 'Test message',
        ]);
    }

    #[Test]
    public function it_blocks_saving_message_to_another_companys_ticket(): void
    {
        $ticket = Ticket::factory()->create(['company_id' => $this->user->company_id + 1]);

        $response = $this->post('/ticket/message/save', [
            'ticket_id' => $ticket->id,
            'message' => 'Cross-tenant message',
        ]);

        $response->assertNotFound();
        $this->assertDatabaseMissing('ticket_message', [
            'ticket_id' => $ticket->id,
        ]);
    }
}
