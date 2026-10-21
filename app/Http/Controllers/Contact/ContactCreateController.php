<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\MainController;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Squire\Models\Country;

class ContactCreateController extends MainController
{
    public function create(Request $request, string $model, string $id_model): View
    {
        $contact = new Contact;
        $contact->{$model.'_id'} = $id_model;

        $countries = Country::orderBy('name')->get();

        return view('contact.contact', compact('contact', 'countries'));
    }
}
