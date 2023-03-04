<?php

namespace App\Http\Controllers\Api\Contact;

use App\Models\Contact;

class ContactReadController
{
    public function read($id)
    {
        // TODO TRY CATCH
        return response()->json(Contact::find($id));
    }
}
