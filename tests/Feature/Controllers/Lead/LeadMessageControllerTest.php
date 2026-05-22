<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use App\Models\Lead\Message as LeadMessage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadMessageControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_message_save(): void
    {
        auth()->guard('web')->logout();

        $response = $this->post('/lead/message/save', []);

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_can_save_lead_message(): void
    {
        $lead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);

        $response = $this->post('/lead/message/save', [
            'lead_id' => $lead->id,
            'message' => 'Test message',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lead_message', [
            'lead_id' => $lead->id,
            'author_id' => $this->user->id,
            'body' => 'Test message',
        ]);
    }

    #[Test]
    public function it_blocks_saving_message_to_another_companys_lead(): void
    {
        $lead = Lead::factory()->create([
            'company_id' => $this->user->company_id + 1,
            'seller_id' => $this->user->id,
        ]);

        $response = $this->post('/lead/message/save', [
            'lead_id' => $lead->id,
            'message' => 'Cross-tenant message',
        ]);

        $response->assertNotFound();
        $this->assertDatabaseMissing('lead_message', [
            'lead_id' => $lead->id,
        ]);
    }

    #[Test]
    public function it_blocks_unauthenticated_message_delete(): void
    {
        $lead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);
        $message = LeadMessage::create([
            'lead_id' => $lead->id,
            'author_id' => $this->user->id,
            'body' => 'Test',
        ]);

        auth()->guard('web')->logout();

        $response = $this->get("/lead/message/delete/{$message->id}");

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_blocks_deleting_another_users_message(): void
    {
        $lead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);
        $message = LeadMessage::create([
            'lead_id' => $lead->id,
            'author_id' => $this->user->id + 1,
            'body' => 'Other user message',
        ]);

        $response = $this->get("/lead/message/delete/{$message->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_can_delete_own_message(): void
    {
        $lead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);
        $message = LeadMessage::create([
            'lead_id' => $lead->id,
            'author_id' => $this->user->id,
            'body' => 'Test',
        ]);

        $response = $this->get("/lead/message/delete/{$message->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('lead_message', ['id' => $message->id]);
    }
}
