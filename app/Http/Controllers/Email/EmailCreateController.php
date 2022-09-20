<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailCreateController extends MainController
{
    public function create(Request $request)
    {
        $email = new Email();
        $data['froms'] = [
            Auth::user()->company->email,
            Auth::user()->email,
        ];
        $data['email'] = $email;

        return view('email.email', $data);
    }
}
