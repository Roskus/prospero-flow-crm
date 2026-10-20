<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $user = User::query()
            ->manageableBy(Auth::user())
            ->findOrFail($id);
        abort_unless(Auth::user()->canManageUser($user), 403);

        $data['user'] = $user;
        $data['languages'] = config('app.locales');
        $data['roles'] = Auth::user()->assignableRoles();
        $data['companies'] = Auth::user()->hasRole('SuperAdmin') ? Company::all() : [];
        $data['timezones'] = timezone_identifiers_list();

        return view('user.user', $data);
    }
}
