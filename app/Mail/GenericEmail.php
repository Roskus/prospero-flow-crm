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
        return new Envelope(
            from: $this->sender(),
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

    /**
     * The sender chosen in the email form (data "from_email") when it is the user's or the
     * company's address; otherwise the user's address, or the company's when there is no user.
     */
    private function sender(): Address
    {
        $fromEmail = $this->data['from_email'] ?? null;

        if ($fromEmail !== null && $fromEmail === $this->company->email) {
            return new Address($this->company->email, $this->company->name);
        }

        if (isset($this->user)) {
            return new Address($this->user->email, $this->user->name);
        }

        return new Address($this->company->email, $this->company->name);
    }
}
