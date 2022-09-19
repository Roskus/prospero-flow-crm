<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Mail\GenericEmail;
use Illuminate\Http\Request;
use App\Models\Email;
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
        $email = Email::find($id);
        $email->status = Email::QUEUE;
        $email->save();
        $emailTemplate = new GenericEmail(Auth::user()->company, $email->subject, ['body' => $email->body]);
        try {
            Mail::to($email->to)->send($emailTemplate);
            $email->status = Email::SENT;
        } catch (\Throwable $t) {
            Log::error($t->getMessage());
            $email->status = Email::ERROR;
        }
        $email->save();
        return redirect('/email');
    }
}
