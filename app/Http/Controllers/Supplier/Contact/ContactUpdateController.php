<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier\Contact;

use App\Http\Controllers\MainController;
use App\Models\Supplier\SupplierContact;
use Illuminate\Support\Facades\Auth;

class ContactUpdateController extends MainController
{
    public function update(int $id)
    {
        $contact = SupplierContact::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        return view('supplier.contact.contact', compact('contact'));
    }
}
