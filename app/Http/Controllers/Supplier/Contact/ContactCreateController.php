<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier\Contact;

use App\Http\Controllers\MainController;
use App\Models\Supplier\SupplierContact;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Squire\Models\Country;

class ContactCreateController extends MainController
{
    public function create(Request $request, string $model, string $id_model): View
    {
        $contact = new SupplierContact;
        $contact->{$model.'_id'} = $id_model;

        $countries = Country::orderBy('name')->get();

        return view('supplier.contact.contact', compact('contact', 'countries'));
    }
}
