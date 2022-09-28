<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserListController extends MainController
{
    public function index(Request $request)
    {
        $user = new User();
        $users = (Auth::user()->company_id == 1) ? $user->getAll() : getAllActiveByCompany(Auth::user()->company_id);
        $data['users'] =  $users;

        return view('user.index', $data);
    }
}
