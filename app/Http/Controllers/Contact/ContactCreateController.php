<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactCreateController extends Controller
{
    public function create(Request $request)
    {
        $contact = new Contact();

        return view('contact.contact', compact('contact'));
    }
}
