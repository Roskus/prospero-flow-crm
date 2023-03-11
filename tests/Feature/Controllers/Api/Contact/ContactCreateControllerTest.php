<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Contact;

use App\Models\Industry;
use App\Models\Lead;
use Tests\TestCase;

class ContactCreateControllerTest extends TestCase
{
    protected $jsonStructureContact = [
        'id',
    ];

    /**
     * @test
     */
    public function can_create_contact(): void
    {
        $this->actingAs($this->user, 'api');

        Industry::factory()->create();
        $lead = Lead::factory()->create();

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

        $this->get('/lead/update/'.$lead->id)
            ->assertSee($data['contact_email'])
            ->assertSee($data['contact_first_name'])
            ->assertSee($data['contact_phone']);
    }

    /**
     * @test
     */
    public function create_contact_have_missing_parameters(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->json('POST', '/api/contact/', [
            'contact_email' => 'email@example.com',
        ]);

        $response->assertStatus(422);

        $response->assertJson([
            'message' => 'The contact first name field is required. (and 2 more errors)',
            'errors' => [
                'contact_first_name' => [
                    'The contact first name field is required.',
                ],
                'lead_id' => [
                    'The lead id field is required.',
                ],
                'contact_phone' => [
                    'The contact phone field is required.',
                ],
            ],
        ]);
    }
}
