<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier\Contact;

use App\Http\Controllers\Controller;
use App\Models\Supplier\SupplierContact;
use Illuminate\Http\Request;

class ContactDeleteController extends Controller
{
    public function delete(Request $request, int $id)
    {
        $contact = SupplierContact::find($id);
        $contact->delete();

        return back();
    }
}
