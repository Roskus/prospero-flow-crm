<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmployee extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Welcome to :app', ['app' => config('app.name')]),
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: nl2br(e(__(
                "Welcome :name!\n\nYour temporary password is: :password\n\nYou will be required to change it on your first login.",
                ['name' => $this->user->first_name, 'password' => $this->plainPassword],
            ))),
        );
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        return [];
    }
}
