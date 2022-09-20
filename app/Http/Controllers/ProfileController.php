<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Profile controller
 */
class ProfileController extends MainController
{
    /**
     * @param  Request  $request
     */
    public function edit(Request $request)
    {
        $data['user'] = User::find(Auth::user()->id);
        $data['languages'] = config('app.locales');

        return view('user.profile', $data);
    }
}
