<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends MainController
{

    public function index(Request $request)
    {
        $user = new User();
        $data['users'] = $user->getAll(); //by company
        return view('user.index', $data);
    }

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        $user = ($request->id) ? User::find($request->id) : new User();
        $user->first_name = $request->first_name;
        $user->lang = $request->lang;
        $user->updated_at = now();
        $user->save();
        return redirect('/profile');
    }
}
