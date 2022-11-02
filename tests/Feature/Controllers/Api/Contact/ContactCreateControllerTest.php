<?php

namespace Tests\Feature\Controllers\Api\Contact;

use App\Models\Industry;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactCreateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $jsonStructureContact = [
        'id',
    ];

    public function test_it_creates_a_contact()
    {
        $user = $this->signin();

        Industry::factory()->create();
        $lead = Lead::factory()->create();

        $data = [
            'lead_id' => $lead->id,
            'email' => 'email@example.com',
            'first_name' => 'Test First Name',
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
        ]);

        $this->get('/lead/update/'.$lead->id)
            ->assertSee($data['email'])
            ->assertSee($data['first_name']);
    }

    public function test_creating_contact_is_not_possible_if_parameters_are_missing()
    {
        $user = $this->signin();

        $response = $this->json('POST', '/api/contact/', [
            'email' => 'email@example.com',
        ]);

        $response->assertStatus(422);

        $response->assertJson([
            'message' => 'The lead id field is required. (and 1 more error)',
            'errors' => [
                'lead_id' => [
                    'The lead id field is required.',
                ],
                'first_name' => [
                    'The first name field is required.',
                ],
            ],
        ]);
    }
}
