<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\GenericEmail;
use App\Models\Email;
use App\Models\User;
use Illuminate\Mail\Attachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailSendService
{
    public function send(Email $email, User $user): Email
    {
        $email->status = Email::QUEUE;
        $email->save();

        $params['from_email'] = $email->from_email;
        $params['body'] = $email->body;

        if (isset($email->signature)) {
            $params['signature'] = $user->signature_html;
        }

        $message = new GenericEmail($user->company, $user, $email->subject, $params);

        try {
            $mail = Mail::to($email->to);

            if ($email->cc) {
                $mail->cc($email->cc);
            }

            if ($email->bcc) {
                $mail->bcc($email->bcc);
            }

            foreach ($email->attachments as $attachment) {
                $path = storage_path('app/'.$attachment->file);
                $file = Attachment::fromPath($path)
                    ->as($attachment->original_name)
                    ->withMime($attachment->mime);
                $message->attach($file);
            }

            $mail->send($message);
            $email->status = Email::SENT;
        } catch (\Throwable $t) {
            Log::error($t->getMessage());
            $email->status = Email::ERROR;
        }

        $email->save();

        return $email;
    }
}
