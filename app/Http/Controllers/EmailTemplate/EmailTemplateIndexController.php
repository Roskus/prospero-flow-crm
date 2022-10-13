<?php

namespace App\Http\Controllers\EmailTemplate;

use App\Http\Controllers\MainController;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateIndexController extends MainController
{
    public function index(Request $request)
    {
        $emailTemplate = new EmailTemplate();
        $data['templates'] = $emailTemplate->getAll();
        return view('email_template.index', $data);
    }
}
