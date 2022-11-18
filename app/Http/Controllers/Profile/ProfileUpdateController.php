<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Profile controller
 */
class ProfileUpdateController extends MainController
{
    /**
     * @param  Request  $request
     * @return Application|Factory|View
     */
    public function update(Request $request)
    {
        $data['user'] = User::find(Auth::user()->id);
        $data['languages'] = config('app.locales');

        return view('user.profile', $data);
    }
}
