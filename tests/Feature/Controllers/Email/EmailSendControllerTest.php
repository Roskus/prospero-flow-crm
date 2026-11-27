<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Mail\GenericEmail;
use App\Models\Email;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailSendControllerTest extends TestCase
{
    #[Test]
    public function it_can_send_email(): void
    {
        Mail::fake();

        $email = Email::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/email/send/'.$email->id);

        $response->assertRedirect('/email');
        Mail::assertSent(GenericEmail::class);
        $this->equalTo($email->status, Email::SENT);
    }

    #[Test]
    public function it_sends_from_the_company_address_when_chosen(): void
    {
        Mail::fake();

        $company = $this->user->company;
        $email = Email::factory()->create([
            'company_id' => $this->user->company_id,
            'from' => $company->email,
        ]);

        $this->get('/email/send/'.$email->id)->assertRedirect('/email');

        Mail::assertSent(GenericEmail::class, function (GenericEmail $mail) use ($company): bool {
            return $mail->hasFrom($company->email, $company->name)
                && ! $mail->hasFrom($this->user->email);
        });
    }

    #[Test]
    public function it_sends_from_the_user_address_when_chosen(): void
    {
        Mail::fake();

        $email = Email::factory()->create([
            'company_id' => $this->user->company_id,
            'from' => $this->user->email,
        ]);

        $this->get('/email/send/'.$email->id)->assertRedirect('/email');

        Mail::assertSent(GenericEmail::class, function (GenericEmail $mail): bool {
            return $mail->hasFrom($this->user->email, $this->user->name);
        });
    }

    #[Test]
    public function it_ignores_a_sender_that_is_not_the_user_or_the_company(): void
    {
        Mail::fake();

        $email = Email::factory()->create([
            'company_id' => $this->user->company_id,
            'from' => 'someone@elsewhere.test',
        ]);

        $this->get('/email/send/'.$email->id)->assertRedirect('/email');

        Mail::assertSent(GenericEmail::class, function (GenericEmail $mail): bool {
            return $mail->hasFrom($this->user->email)
                && ! $mail->hasFrom('someone@elsewhere.test');
        });
    }

    #[Test]
    public function it_sends_without_the_signature_when_it_is_not_included(): void
    {
        Mail::fake();

        $this->user->signature_html = '<p>User signature block</p>';
        $this->user->save();
        $email = Email::factory()->create(['company_id' => $this->user->company_id, 'signature' => false]);

        $this->get('/email/send/'.$email->id)->assertRedirect('/email');

        Mail::assertSent(GenericEmail::class, function (GenericEmail $mail): bool {
            return ! str_contains($mail->render(), 'User signature block');
        });
    }
}
