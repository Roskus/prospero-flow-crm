<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        $user = ($request->id) ? User::find($request->id) : new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->lang = $request->lang;
        $user->updated_at = now();
        if (!empty($request->password) && !empty($request->password_confirmation) && ($request->password == $request->password_confirmation) ) {
            $user->password = Hash::make($request->password);
        }
        $user->updated_at = now();
        $user->save();
        return redirect('/user');
    }
}
