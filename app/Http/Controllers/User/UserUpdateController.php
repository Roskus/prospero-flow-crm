<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $user = User::find($id);
        $data['user'] = $user;
        $data['languages'] = config('app.locales');
        $data['roles'] = Role::all();

        return view('user.user', $data);
    }
}
