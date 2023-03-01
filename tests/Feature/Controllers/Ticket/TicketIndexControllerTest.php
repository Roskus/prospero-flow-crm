<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Ticket;

use App\Models\Ticket;
use App\Models\User;
use Tests\TestCase;

class TicketIndexControllerTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_view_index_tickets(): void
    {
        $ticket = Ticket::factory()->create(['company_id' => $this->user->company_id]);
        $ticket2 = Ticket::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/ticket');

        $response->assertOk();
        $response->assertSee($ticket->title);
        $response->assertSee($ticket2->title);
    }

    /** @test */
    public function it_can_search_tickets(): void
    {
        $ticket = Ticket::factory()->create(['company_id' => $this->user->company_id]);
        $ticket2 = Ticket::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/ticket?search='.$ticket->title);
        $response->assertOk();
        $response->assertSee($ticket->title);
        $response->assertDontSee($ticket2->title);
    }
}
