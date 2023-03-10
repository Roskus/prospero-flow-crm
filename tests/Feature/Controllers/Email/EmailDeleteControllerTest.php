<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use App\Models\User;
use Tests\TestCase;

class EmailDeleteControllerTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_delete_email(): void
    {
        $email = Email::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('email/delete/'.$email->id);

        $response->assertRedirect('email');
        $response->assertSessionHas('message', 'Email deleted successfully');
        $this->assertDatabaseMissing('email', $email->toArray());
    }
}
