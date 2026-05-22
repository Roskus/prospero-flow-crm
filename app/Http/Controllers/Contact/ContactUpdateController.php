<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\MainController;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;

class ContactUpdateController extends MainController
{
    public function update(int $id)
    {
        $contact = Contact::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        return view('contact.contact', compact('contact'));
    }
}
