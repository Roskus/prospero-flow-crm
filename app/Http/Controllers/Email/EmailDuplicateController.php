<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\Email;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailDuplicateController extends Controller
{
    public function duplicate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email_id' => 'required|integer',
            'email_to' => 'required|email',
        ]);

        $email = Email::find($validated['email_id']);
        $email_to = $validated['email_to'];

        $email_duplicate = collect($email->replicate())->except(['cc', 'schedule_send']);
        $email_duplicate['from'] = '';
        $email_duplicate['to'] = $email_to;
        $email_duplicate['status'] = Email::DRAFT;

        $email_duplicate = Email::create($email_duplicate->toArray());

        return redirect('email/update/'.$email_duplicate->id);
    }
}
