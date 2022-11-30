<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\MainController;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactSaveController extends MainController
{
    public function save(Request $request)
    {
        if (empty($request->id)) {
            $contact = new Contact();
            $contact->created_at = now();
        } else {
            $contact = Contact::find($request->id);
        }

        $contact->lead_id = $request->lead_id;
        $contact->customer_id = $request->customer_id;

        $contact->first_name = $request->contact_first_name;
        $contact->last_name = $request->contact_last_name;
        $contact->phone = $request->contact_phone;
        $contact->email = strtolower(trim($request->contact_email));
        $contact->linkedin = $request->contact_linkedin;
        //$contact->country = $request->contact_country;
        $contact->notes = $request->contact_notes;
        $contact->updated_at = now();
        try {
            $contact->save();
        } catch (\Throwable $t) {
            Log::error($t->getMessage());
        }

        return back();
    }
}
