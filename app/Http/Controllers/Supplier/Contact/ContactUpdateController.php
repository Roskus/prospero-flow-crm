<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier\Contact;

use App\Http\Controllers\Controller;
use App\Models\Supplier\SupplierContact;

class ContactUpdateController extends Controller
{
    public function update(int $id)
    {
        $contact = SupplierContact::find($id);

        return view('supplier.contact.contact', compact('contact'));
    }
}
