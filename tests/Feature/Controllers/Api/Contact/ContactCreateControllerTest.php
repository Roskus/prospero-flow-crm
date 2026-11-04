<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Contact;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\Industry;
use App\Models\Lead;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ContactCreateControllerTest extends TestCase
{
    protected array $jsonStructureContact = [
        'id',
    ];

    #[Test]
    public function can_create_contact(): void
    {
        $this->actingAs($this->user, 'api');

        Industry::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id]);

        $data = [
            'lead_id' => $lead->id,
            'contact_first_name' => 'Test First Name',
            'contact_email' => 'email@example.com',
            'contact_phone' => 666666666,
        ];

        $response = $this->json('POST', '/api/contact/', $data);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'contact' => $this->jsonStructureContact,
        ]);

        $contact_id = $response->json('contact.id');
        $response->assertJsonFragment([
            'contact' => [
                'id' => $contact_id,
            ],
        ]);

        $this->assertDatabaseHas('contact', [
            'id' => $contact_id,
            'lead_id' => $lead->id,
            'email' => 'email@example.com',
            'first_name' => 'Test First Name',
            'phone' => 666666666,
        ]);

        $this->actingAs($this->user, 'web');
        $this->get('/lead/update/'.$lead->id)
            ->assertSee($data['contact_email'])
            ->assertSee($data['contact_first_name'])
            ->assertSee($data['contact_phone']);
    }

    #[Test]
    public function create_contact_have_missing_parameters(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->json('POST', '/api/contact/', [
            'contact_email' => 'email@example.com',
        ]);

        $response->assertStatus(422);

        $response->assertJson([
            'message' => 'The contact first name field is required. (and 1 more error)',
            'errors' => [
                'contact_first_name' => [
                    'The contact first name field is required.',
                ],
                'lead_id' => [
                    'The lead id field is required when customer id is not present.',
                ],
            ],
        ]);

        $response->assertJsonMissingValidationErrors(['contact_email', 'contact_phone']);
    }

    #[Test]
    public function can_create_contact_without_email_and_phone(): void
    {
        $this->actingAs($this->user, 'api');

        Industry::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->json('POST', '/api/contact/', [
            'lead_id' => $lead->id,
            'contact_first_name' => 'No Email Contact',
            'contact_last_name' => 'Smith',
            'contact_job_title' => 'Head of Sales',
            'contact_linkedin' => 'https://www.linkedin.com/in/no-email-contact',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('contact', [
            'id' => $response->json('contact.id'),
            'lead_id' => $lead->id,
            'first_name' => 'No Email Contact',
            'last_name' => 'Smith',
            'job_title' => 'Head of Sales',
            'email' => null,
            'phone' => null,
            'linkedin' => 'https://www.linkedin.com/in/no-email-contact',
        ]);
    }

    #[Test]
    public function can_create_contact_with_location(): void
    {
        $this->actingAs($this->user, 'api');

        Industry::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->json('POST', '/api/contact/', [
            'lead_id' => $lead->id,
            'contact_first_name' => 'Located Contact',
            'country_id' => 'ES',
            'province' => 'Madrid',
            'city' => 'Madrid',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('contact', [
            'id' => $response->json('contact.id'),
            'country_id' => 'es',
            'province' => 'Madrid',
            'city' => 'Madrid',
        ]);
    }

    #[Test]
    public function create_contact_rejects_invalid_country(): void
    {
        $this->actingAs($this->user, 'api');

        Industry::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id]);

        $this->json('POST', '/api/contact/', [
            'lead_id' => $lead->id,
            'contact_first_name' => 'Nowhere Contact',
            'country_id' => 'zz',
        ])->assertStatus(422)->assertJsonValidationErrors(['country_id']);
    }

    #[Test]
    public function create_contact_rejects_invalid_email(): void
    {
        $this->actingAs($this->user, 'api');

        Industry::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->json('POST', '/api/contact/', [
            'lead_id' => $lead->id,
            'contact_first_name' => 'Invalid Email',
            'contact_email' => 'not-an-email',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['contact_email']);
    }

    #[Test]
    public function can_create_contact_for_a_customer(): void
    {
        $this->actingAs($this->user, 'api');

        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->json('POST', '/api/contact/', [
            'customer_id' => $customer->id,
            'contact_first_name' => 'Customer Contact',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('contact', [
            'id' => $response->json('contact.id'),
            'customer_id' => $customer->id,
            'lead_id' => null,
            'company_id' => $this->user->company_id,
        ]);
    }

    #[Test]
    public function cannot_create_contact_for_another_companys_lead(): void
    {
        $this->actingAs($this->user, 'api');

        Industry::factory()->create();
        $foreignLead = Lead::factory()->create(['company_id' => $this->user->company_id + 1]);

        $this->json('POST', '/api/contact/', [
            'lead_id' => $foreignLead->id,
            'contact_first_name' => 'Intruder',
        ])->assertStatus(422)->assertJsonValidationErrors(['lead_id']);

        $this->assertDatabaseMissing('contact', ['lead_id' => $foreignLead->id]);
    }

    #[Test]
    public function cannot_create_contact_for_another_companys_customer(): void
    {
        $this->actingAs($this->user, 'api');

        $foreignCustomer = Customer::factory()->create(['company_id' => $this->user->company_id + 1]);

        $this->json('POST', '/api/contact/', [
            'customer_id' => $foreignCustomer->id,
            'contact_first_name' => 'Intruder',
        ])->assertStatus(422)->assertJsonValidationErrors(['customer_id']);

        $this->assertDatabaseMissing('contact', ['customer_id' => $foreignCustomer->id]);
    }

    #[Test]
    public function cannot_move_contact_to_another_companys_lead_on_update(): void
    {
        $this->actingAs($this->user, 'api');

        Industry::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id]);
        $foreignLead = Lead::factory()->create(['company_id' => $this->user->company_id + 1]);
        $contact = Contact::factory()->create([
            'company_id' => $this->user->company_id,
            'lead_id' => $lead->id,
            'customer_id' => null,
        ]);

        $this->json('PUT', '/api/contact/'.$contact->id, [
            'lead_id' => $foreignLead->id,
            'contact_first_name' => 'Moved',
        ])->assertStatus(422)->assertJsonValidationErrors(['lead_id']);

        $this->assertDatabaseHas('contact', ['id' => $contact->id, 'lead_id' => $lead->id]);
    }

    #[Test]
    public function cannot_create_contact_for_a_deleted_lead(): void
    {
        $this->actingAs($this->user, 'api');

        Industry::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id]);
        $lead->delete();

        $this->json('POST', '/api/contact/', [
            'lead_id' => $lead->id,
            'contact_first_name' => 'Deleted Lead Contact',
        ])->assertStatus(422)->assertJsonValidationErrors(['lead_id']);
    }

    /**
     * Business rule: sellers can add contacts to any lead of their company, not only their own.
     */
    #[Test]
    public function seller_can_create_contact_for_a_lead_assigned_to_another_seller(): void
    {
        Industry::factory()->create();
        $seller = $this->sellerOfUserCompany();
        $otherSeller = User::factory()->create(['company_id' => $this->user->company_id]);
        $otherSellersLead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $otherSeller->id,
        ]);

        $this->actingAs($seller, 'api')->json('POST', '/api/contact/', [
            'lead_id' => $otherSellersLead->id,
            'contact_first_name' => 'Not Mine',
        ])->assertStatus(201);

        $this->assertDatabaseHas('contact', ['lead_id' => $otherSellersLead->id, 'first_name' => 'Not Mine']);
    }

    #[Test]
    public function seller_can_create_contact_for_own_lead(): void
    {
        Industry::factory()->create();
        $seller = $this->sellerOfUserCompany();
        $ownLead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $seller->id,
        ]);

        $this->actingAs($seller, 'api')->json('POST', '/api/contact/', [
            'lead_id' => $ownLead->id,
            'contact_first_name' => 'Mine',
        ])->assertStatus(201);

        $this->assertDatabaseHas('contact', ['lead_id' => $ownLead->id, 'first_name' => 'Mine']);
    }

    private function sellerOfUserCompany(): User
    {
        $seller = User::factory()->create(['company_id' => $this->user->company_id]);
        $seller->assignRole('User');
        $seller->givePermissionTo(Permission::findOrCreate('create contact', 'web'));

        return $seller;
    }
}
