<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserSaveController extends MainController
{
    public function save(UserRequest $request)
    {
        if (empty($request->id)) {
            $user = new User;
            $user->created_at = now();
            //Company should be assigned on create
            $user->company_id = Auth::user()->company_id;
        } else {
            $user = User::find($request->id);
        }

        if (Auth::user()->hasRole('SuperAdmin')) {
            $user->company_id = $request->company_id;
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->lang = $request->lang;
        if ($request->timezone) {
            $user->timezone = $request->timezone;
        }

        if (
            ! empty($request->password) &&
            ! empty($request->password_confirmation) &&
            ($request->password == $request->password_confirmation)
        ) {
            $user->password = Hash::make($request->password);
        }

        if (! empty($request->roles)) {
            $user->syncRoles($request->roles);
        }

        $user->updated_at = now();
        $user->save();

        return redirect('/user');
    }
}
