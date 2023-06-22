<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Mail\GenericEmail;
use App\Models\Email;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Attachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailSendController extends MainController
{
    /**
     * Save email in queue
     * Change email status to QUEUE (Pending)
     */
    public function send(Request $request, int $id)
    {
        $email = Email::findOrFail($id);
        $email->status = Email::QUEUE;
        $email->save();
        $params['from_email'] = $email->from_email;
        $params['body'] = $email->body;

        if (isset($email->signature)) {
            $params['signature'] = Auth::user()->signature_html;
        }

        $user = User::find(Auth::user()->id);
        /**
         * @TODO Refactor this as a Service
         */
        $message = new GenericEmail(Auth::user()->company, $user, $email->subject, $params);
        try {
            $mail = Mail::to($email->to);
            if ($email->cc) {
                $mail->cc($email->cc);
            }

            if ($email->bcc) {
                $mail->bcc($email->bcc);
            }

            if ($email->attachments()->count() > 0) {
                foreach ($email->attachments()->get() as $attachment) {
                    $path = storage_path('app/'.$attachment->file);
                    $file = Attachment::fromPath($path)
                        ->as($attachment->original_name)
                        ->withMime($attachment->mime);
                    $message->attach($file);
                }
            }
            $mail->send($message);
            $email->status = Email::SENT;
        } catch (\Throwable $t) {
            Log::error($t->getMessage());
            $email->status = Email::ERROR;
        }
        $email->save();

        return redirect('/email');
    }
}
