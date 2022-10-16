<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Mail\Mailable;

class GenericEmail extends Mailable
{
    protected Company $company;

    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(?Company $company = null, ?string $subject = '', ?array $data = [])
    {
        $this->company = $company;
        $this->subject = $subject;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->company->email, $this->company->name)
            ->subject($this->subject)
            ->view('mail.generic', $this->data);
    }
}
