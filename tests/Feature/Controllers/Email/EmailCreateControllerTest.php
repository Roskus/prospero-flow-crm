<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\User;
use Tests\TestCase;

class EmailCreateControllerTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_create_email(): void
    {
        $response = $this->get('/email/create');

        $response->assertOk();
        $response->assertViewIs('email.email');
        $response->assertSee('Subject');
        $response->assertSee('From');
        $response->assertSee('Attachments');
    }
}
