<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\Email;
use App\Services\EmailSendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailSendController extends MainController
{
    /**
     * Save email in queue
     * Change email status to QUEUE (Pending)
     */
    public function send(Request $request, int $id)
    {
        $email = Email::where('company_id', Auth::user()->company_id)->findOrFail($id);

        app(EmailSendService::class)->send($email, Auth::user());

        return redirect('/email');
    }
}
