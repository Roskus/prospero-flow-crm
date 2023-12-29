<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;

class ContactUpdateController extends Controller
{
    public function update(int $id)
    {
        $contact = Contact::find($id);

        return view('contact.contact', compact('contact'));
    }
}
