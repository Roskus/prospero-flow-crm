<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{

    public function index()
    {
        $user = new User();
        $data['users'] = $user->getAll(); //by company
        return view('user.index',$data);
    }

    public function profile()
    {
        return view('user.profile');
    }
}
