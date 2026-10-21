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

        $response->assertRedirect(route('login'));
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

        $response->assertRedirect(route('login'));
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

        $response->assertRedirect(route('login'));
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

    #[Test]
    public function it_saves_a_supplier_contact_with_address(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/supplier/contact/save', [
            'supplier_id' => $supplier->id,
            'contact_first_name' => 'Jane',
            'contact_email' => 'jane@example.com',
            'country_id' => 'ar',
            'city' => 'Rosario',
        ]);

        $response->assertSessionHasNoErrors();
        $contact = SupplierContact::where('supplier_id', $supplier->id)->firstOrFail();
        $this->assertSame('Rosario, Argentina', $contact->address->oneLine());
    }

    #[Test]
    public function it_rejects_a_supplier_contact_with_an_unknown_country(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/supplier/contact/save', [
            'supplier_id' => $supplier->id,
            'contact_first_name' => 'Jane',
            'contact_email' => 'jane@example.com',
            'country_id' => 'zz',
        ]);

        $response->assertSessionHasErrors('country_id');
    }

    #[Test]
    public function the_supplier_contact_list_shows_country_and_city(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => $this->user->company_id]);
        SupplierContact::factory()->create([
            'company_id' => $this->user->company_id,
            'supplier_id' => $supplier->id,
            'country_id' => 'ar',
            'city' => 'Rosario',
        ]);

        $response = $this->get('/supplier/update/'.$supplier->id);

        $response->assertOk();
        $response->assertSeeInOrder(['Argentina', 'Rosario']);
    }
}
