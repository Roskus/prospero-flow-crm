<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Supplier\Contact;

use App\Models\Supplier;
use App\Models\Supplier\SupplierContact;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SupplierContactControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_contact_create(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => $this->user->company_id]);

        auth()->guard('web')->logout();

        $response = $this->get("/supplier/contact/create/supplier/{$supplier->id}");

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_can_show_supplier_contact_create_form(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get("/supplier/contact/create/supplier/{$supplier->id}");

        $response->assertOk();
    }

    #[Test]
    public function it_blocks_unauthenticated_contact_update(): void
    {
        $contact = SupplierContact::factory()->create(['company_id' => $this->user->company_id]);

        auth()->guard('web')->logout();

        $response = $this->get("/supplier/contact/update/{$contact->id}");

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_can_show_supplier_contact_update_form(): void
    {
        $contact = SupplierContact::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get("/supplier/contact/update/{$contact->id}");

        $response->assertOk();
    }

    #[Test]
    public function it_blocks_updating_another_companys_supplier_contact(): void
    {
        $contact = SupplierContact::factory()->create(['company_id' => $this->user->company_id + 1]);

        $response = $this->get("/supplier/contact/update/{$contact->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_blocks_unauthenticated_contact_delete(): void
    {
        $contact = SupplierContact::factory()->create(['company_id' => $this->user->company_id]);

        auth()->guard('web')->logout();

        $response = $this->get("/supplier/contact/delete/{$contact->id}");

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_can_delete_own_supplier_contact(): void
    {
        $contact = SupplierContact::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get("/supplier/contact/delete/{$contact->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('supplier_contact', ['id' => $contact->id]);
    }

    #[Test]
    public function it_blocks_deleting_another_companys_supplier_contact(): void
    {
        $contact = SupplierContact::factory()->create(['company_id' => $this->user->company_id + 1]);

        $response = $this->get("/supplier/contact/delete/{$contact->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_prevents_blind_write_to_another_companys_supplier_contact(): void
    {
        $contactFromOtherCompany = SupplierContact::factory()->create(['company_id' => $this->user->company_id + 1]);
        $originalName = $contactFromOtherCompany->first_name;

        $updateData = [
            'id' => $contactFromOtherCompany->id,
            'contact_first_name' => 'Hijacked',
            'contact_last_name' => 'Name',
            'contact_email' => 'hijacked@example.com',
        ];

        $response = $this->post('/supplier/contact/save', $updateData);
        $response->assertNotFound();

        $contactFromOtherCompany->refresh();
        $this->assertEquals($originalName, $contactFromOtherCompany->first_name);
    }

    #[Test]
    public function it_prevents_exporting_another_companys_supplier_contact_vcard(): void
    {
        $contactFromOtherCompany = SupplierContact::factory()->create(['company_id' => $this->user->company_id + 1]);

        $response = $this->get("/supplier/contact/export-vcard/{$contactFromOtherCompany->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_can_export_own_supplier_contact_vcard(): void
    {
        $contact = SupplierContact::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get("/supplier/contact/export-vcard/{$contact->id}");

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }
}
