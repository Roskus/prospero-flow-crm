<?php

namespace Tests\Feature\Controllers\Lead;

use App\Models\User;
use Tests\TestCase;

class LeadImportIndexControllerTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_index_import_lead()
    {
        $response = $this->get('/lead/import');

        $response->assertOk();
        $response->assertViewIs('lead.import');
        $response->assertSee('Download example file');
        $response->assertSee('Save');
        $response->assertSee('Separator');
    }
}
