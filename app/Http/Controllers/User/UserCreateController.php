<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\Company;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserCreateController extends MainController
{
    /**
     * @return Application|Factory|View
     */
    public function create(Request $request)
    {
        $user = new User;
        $data['user'] = $user;
        $data['languages'] = config('app.locales');
        $data['roles'] = Role::all();
        $data['companies'] = Auth::user()->hasRole('SuperAdmin') ? Company::all() : [];
        $data['timezones'] = timezone_identifiers_list();

        return view('user.user', $data);
    }
}
