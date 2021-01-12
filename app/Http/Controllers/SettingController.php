<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends MainController
{
    public function index()
    {
        return view('setting.index');
    }
}
