<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends MainController
{
    public function index(Request $request)
    {
        return view('setting.index');
    }
}
