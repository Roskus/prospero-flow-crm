<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

    public function save(Request $request)
    {
        $locale = $request->lang;
        $user = User::find(Auth::user()->id);
        $user->first_name = $request->first_name;
        $user->lang = $locale;
        $user->updated_at = now();
        $user->save();

        App::setLocale($locale);
        session()->put('locale', $locale);

        return redirect('/profile');
    }
}
