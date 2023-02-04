<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $user = User::find($id);
        $data['user'] = $user;
        $data['languages'] = config('app.locales');
        $data['roles'] = Role::all();
        $data['companies'] = Auth::user()->hasRole('SuperAdmin') ? Company::all() : [];

        return view('user.user', $data);
    }
}
