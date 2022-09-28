<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends MainController
{


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
