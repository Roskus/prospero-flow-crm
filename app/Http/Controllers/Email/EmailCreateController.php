<?php

namespace App\Http\Controllers\Email;

use Illuminate\Http\Request;
use App\Http\Controllers\MainController;

class EmailCreateController extends MainController
{
    public function create(Request $request)
    {

        return view('email.create');
    }
}
