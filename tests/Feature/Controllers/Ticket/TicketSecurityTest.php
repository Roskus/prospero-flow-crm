<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Ticket;

use App\Models\Ticket;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TicketSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::findByName('SuperAdmin');
        $this->user->assignRole($role);
    }

    #[Test]
    public function it_prevents_viewing_tickets_from_other_companies(): void
    {
        $ticketFromOtherCompany = Ticket::factory()->create(['company_id' => $this->user->company_id + 1]);

        $response = $this->get("/ticket/update/{$ticketFromOtherCompany->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_prevents_hijacking_tickets_from_other_companies(): void
    {
        $ticketFromOtherCompany = Ticket::factory()->create(['company_id' => $this->user->company_id + 1]);
        $originalCompanyId = $ticketFromOtherCompany->company_id;

        $updateData = [
            'id' => $ticketFromOtherCompany->id,
            'title' => 'Hijacked Ticket',
            'description' => 'This ticket was hijacked',
            'priority' => 'urgent',
            'type' => 'incident',
            'status' => 'new',
        ];

        $response = $this->post('/ticket/save', $updateData);
        $response->assertNotFound();

        $ticketFromOtherCompany->refresh();
        $this->assertEquals($originalCompanyId, $ticketFromOtherCompany->company_id);
    }

    #[Test]
    public function it_prevents_deleting_tickets_from_other_companies(): void
    {
        $ticketFromOtherCompany = Ticket::factory()->create(['company_id' => $this->user->company_id + 1]);

        $response = $this->get("/ticket/delete/{$ticketFromOtherCompany->id}");

        $response->assertNotFound();

        $this->assertFalse($ticketFromOtherCompany->fresh()->trashed());
    }

    #[Test]
    public function it_can_view_own_ticket(): void
    {
        $ownTicket = Ticket::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get("/ticket/update/{$ownTicket->id}");

        $response->assertOk();
        $response->assertViewHas('ticket', $ownTicket);
    }

    #[Test]
    public function it_can_update_own_ticket(): void
    {
        $ownTicket = Ticket::factory()->create(['company_id' => $this->user->company_id]);

        $updateData = [
            'id' => $ownTicket->id,
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'priority' => 'high',
            'type' => 'question',
            'status' => 'assigned',
        ];

        $response = $this->post('/ticket/save', $updateData);

        $response->assertRedirect('/ticket');
        $ownTicket->refresh();
        $this->assertEquals('Updated Title', $ownTicket->title);
        $this->assertEquals($this->user->company_id, $ownTicket->company_id);
    }

    #[Test]
    public function it_can_delete_own_ticket(): void
    {
        $ownTicket = Ticket::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get("/ticket/delete/{$ownTicket->id}");

        $response->assertRedirect();
        $this->assertTrue($ownTicket->fresh()->trashed());
    }
}
