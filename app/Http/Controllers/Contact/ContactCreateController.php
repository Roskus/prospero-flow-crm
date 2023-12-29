<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactCreateController extends Controller
{
    public function create(Request $request, string $model, string $id_model): View
    {
        $contact = new Contact();
        $contact->{$model.'_id'} = $id_model;

        return view('contact.contact', compact('contact'));
    }
}
