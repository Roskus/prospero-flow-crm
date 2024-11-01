<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_email(): void
    {
        $email = Email::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('email/delete/'.$email->id);

        $response->assertRedirect('email');
        $response->assertSessionHas('message', 'Email deleted successfully');
        $this->assertDatabaseMissing('email', $email->toArray());
    }
}
