<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserCreateController extends MainController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        $user = new User();
        $data['user'] = $user;
        $data['languages'] = config('app.locales');
        $data['roles'] = Role::all();
        return view('user.user', $data);
    }
}
