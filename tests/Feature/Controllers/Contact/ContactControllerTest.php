<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Contact;

use App\Models\Contact;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_contact_create(): void
    {
        auth()->guard('web')->logout();

        $response = $this->get('/contact/create/customer/1');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_can_show_contact_create_form(): void
    {
        $response = $this->get('/contact/create/customer/1');

        $response->assertOk();
    }

    #[Test]
    public function it_blocks_unauthenticated_contact_update(): void
    {
        $contact = Contact::factory()->create(['company_id' => $this->user->company_id]);

        auth()->guard('web')->logout();

        $response = $this->get("/contact/update/{$contact->id}");

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_can_show_contact_update_form(): void
    {
        $contact = Contact::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get("/contact/update/{$contact->id}");

        $response->assertOk();
    }

    #[Test]
    public function it_blocks_updating_another_companys_contact(): void
    {
        $contact = Contact::factory()->create(['company_id' => $this->user->company_id + 1]);

        $response = $this->get("/contact/update/{$contact->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_blocks_unauthenticated_contact_delete(): void
    {
        $contact = Contact::factory()->create(['company_id' => $this->user->company_id]);

        auth()->guard('web')->logout();

        $response = $this->get("/contact/delete/{$contact->id}");

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_can_delete_own_contact(): void
    {
        $contact = Contact::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get("/contact/delete/{$contact->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('contact', ['id' => $contact->id]);
    }

    #[Test]
    public function it_blocks_deleting_another_companys_contact(): void
    {
        $contact = Contact::factory()->create(['company_id' => $this->user->company_id + 1]);

        $response = $this->get("/contact/delete/{$contact->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_prevents_blind_write_to_another_companys_contact(): void
    {
        $contactFromOtherCompany = Contact::factory()->create(['company_id' => $this->user->company_id + 1]);
        $originalName = $contactFromOtherCompany->first_name;

        $updateData = [
            'id' => $contactFromOtherCompany->id,
            'contact_first_name' => 'Hijacked',
            'contact_last_name' => 'Name',
            'contact_email' => 'hijacked@example.com',
        ];

        $response = $this->post('/contact/save', $updateData);
        $response->assertNotFound();

        $contactFromOtherCompany->refresh();
        $this->assertEquals($originalName, $contactFromOtherCompany->first_name);
    }

    #[Test]
    public function it_prevents_exporting_another_companys_contact_vcard(): void
    {
        $contactFromOtherCompany = Contact::factory()->create(['company_id' => $this->user->company_id + 1]);

        $response = $this->get("/contact/export-vcard/{$contactFromOtherCompany->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_can_export_own_contact_vcard(): void
    {
        $contact = Contact::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get("/contact/export-vcard/{$contact->id}");

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }
}
