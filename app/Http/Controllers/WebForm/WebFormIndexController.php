<?php

namespace App\Http\Controllers\WebForm;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;

class WebFormIndexController extends MainController
{
    public function index(Request $request)
    {
        return view('web_form.index');
    }
}