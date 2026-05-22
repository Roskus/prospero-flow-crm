<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\Email;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailDuplicateController extends MainController
{
    public function duplicate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email_id' => 'required|integer',
            'to' => 'required|email',
        ]);

        $email = Email::where('id', $validated['email_id'])
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();
        $to = $validated['to'];

        $email_duplicate = collect($email->replicate())->except(['cc', 'schedule_send']);
        $email_duplicate['from'] = $email->from;
        $email_duplicate['to'] = $to;
        $email_duplicate['status'] = Email::DRAFT;

        $email_duplicate = Email::create($email_duplicate->toArray());

        return redirect('email/update/'.$email_duplicate->id);
    }
}
