<?php

namespace App\Http\Controllers\Email;

use App\Models\Email;
use Illuminate\Http\Request;
use App\Http\Controllers\MainController;

class EmailCreateController extends MainController
{
    public function create(Request $request)
    {
        $email = new Email();
        $data['email'] = $email;
        return view('email.email', $data);
    }
}
