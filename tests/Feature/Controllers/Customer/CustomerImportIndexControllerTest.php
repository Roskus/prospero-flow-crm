<?php

namespace Tests\Feature\Controllers\Customer;

use App\Models\User;
use Tests\TestCase;

class CustomerImportIndexControllerTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_index_import_customer()
    {
        $response = $this->get('/customer/import');

        $response->assertOk();
        $response->assertViewIs('customer.import');
        $response->assertSee('Download example file');
        $response->assertSee('Save');
        $response->assertSee('Separator');
    }
}
