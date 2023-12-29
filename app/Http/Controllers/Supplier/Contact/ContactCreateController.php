<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier\Contact;

use App\Http\Controllers\Controller;
use App\Models\Supplier\SupplierContact;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactCreateController extends Controller
{
    public function create(Request $request, string $model, string $id_model): View
    {
        $contact = new SupplierContact();
        $contact->{$model.'_id'} = $id_model;

        return view('supplier.contact.contact', compact('contact'));
    }
}
