<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Company;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;

class GenericEmail extends Mailable
{
    protected Company $company;

    protected ?array $data;

    protected ?User $user;

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $fromCompany = ! isset($this->user) || ($this->data['from_email'] ?? null) === $this->company->email;
        $sender = $fromCompany ? $this->company : $this->user;

        return new Envelope(
            from: new Address($sender->email, $sender->name),
            subject: $this->subject,
        );
    }

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(?Company $company = null, ?User $user = null, ?string $subject = '', ?array $data = [])
    {
        $this->company = $company;
        $this->subject = $subject;
        $this->data = $data;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.generic', $this->data);
    }
}
