<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserCreateController extends MainController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        $user = new User();
        $data['user'] = $user;
        $data['languages'] = config('app.locales');
        $data['roles'] = Role::all();
        $data['companies'] = Auth::user()->hasRole('SuperAdmin') ? Company::all() : [];
        $data['timezones'] = timezone_identifiers_list();

        return view('user.user', $data);
    }
}
