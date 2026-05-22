<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier\Contact;

use App\Http\Controllers\MainController;
use App\Models\Supplier\SupplierContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ContactDeleteController extends MainController
{
    public function delete(int $id): RedirectResponse
    {
        $contact = SupplierContact::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        $contact->delete();

        return back();
    }
}
