<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserSaveController extends MainController
{
    /**
     * @param  Request  $request
     */
    public function save(Request $request)
    {
        $user = ($request->id) ? User::find($request->id) : new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->lang = $request->lang;
        $user->updated_at = now();
        if (! empty($request->password) && ! empty($request->password_confirmation) && ($request->password == $request->password_confirmation)) {
            $user->password = Hash::make($request->password);
        }
        $user->updated_at = now();
        $user->save();

        return redirect('/user');
    }
}
