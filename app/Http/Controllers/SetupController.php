<?php

namespace App\Http\Controllers;

class SetupController extends Controller
{
    public function index()
    {
        return view('setup/step1');
    }
}
