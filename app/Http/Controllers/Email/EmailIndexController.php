<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\Email;
use Illuminate\Http\Request;

class EmailIndexController extends MainController
{
    public function index(Request $request)
    {
        $email = new Email();
        $data['emails'] = $email->getAll();
        return view('email.index', $data);
    }
}
