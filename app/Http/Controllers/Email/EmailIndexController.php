<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailIndexController extends MainController
{
    public function index(Request $request)
    {
        /*
        $emailTemplate = new EmailTemplate();
        $data['templates'] = $emailTemplate->getAll();
        */
        return view('email.index');
    }
}
