<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Contact;

use App\Models\Contact;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContactDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_a_contact(): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $contact = Contact::factory()->create(['company_id' => auth()->user()->company_id]);

        $response = $this->deleteJson('/api/contact/'.$contact->id);

        $response->assertOk();
        $this->assertSoftDeleted($contact);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_contact(): void
    {
        $this->actingAs(User::factory()->create(), 'api');

        $response = $this->deleteJson('/api/contact/99999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_cannot_delete_a_contact_from_another_company(): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $otherContact = Contact::factory()->create(['company_id' => User::factory()->create()->company_id]);

        $response = $this->deleteJson('/api/contact/'.$otherContact->id);

        $response->assertNotFound();
        $this->assertNotSoftDeleted($otherContact);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->deleteJson('/api/contact/'.$contact->id);

        $response->assertUnauthorized();
    }
}
