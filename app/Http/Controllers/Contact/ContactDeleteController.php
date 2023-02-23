<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactDeleteController extends Controller
{
    public function delete(Request $request, int $id)
    {
        $contact = Contact::find($id);
        $contact->delete();

        return back();
    }
}
