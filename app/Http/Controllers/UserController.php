<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends MainController
{
    public function index(Request $request)
    {
        $user = new User();
        $data['users'] = $user->getAll(); //by company

        return view('user.index', $data);
    }

    public function add(Request $request)
    {
        $user = new User();
        $data['user'] = $user;
        $data['languages'] = config('app.locales');

        return view('user.user', $data);
    }

    public function edit(Request $request, int $id)
    {
        $user = User::find($id);
        $data['user'] = $user;
        $data['languages'] = config('app.locales');

        return view('user.user', $data);
    }


}
