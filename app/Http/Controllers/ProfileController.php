<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Profile controller
 */
class ProfileController extends MainController
{

    /**
     * @param Request $request
     */
    public function edit(Request $request)
    {
        $data['user'] = User::find(Auth::user()->id);
        $data['languages'] = config('app.locales');
        return view('user.profile', $data);
    }

    /**
     * Save user profile
     *
     * @param Request $request HTTP request
     */
    public function save(Request $request)
    {
        $locale = $request->lang;
        $user = User::find(Auth::user()->id);
        $user->first_name = $request->first_name;
        $user->email = $request->email;
        $user->lang = $locale;
        //Update password if change
        if (!empty($request->password) && !empty($request->password_confirmation) && ($request->password == $request->password_confirmation) ) {
            $user->password = Hash::make($request->password);
        }
        $user->updated_at = now();
        $user->save();
        //Update current software language
        App::setLocale($locale);
        session()->put('locale', $locale);
        return redirect('/profile');
    }
}
