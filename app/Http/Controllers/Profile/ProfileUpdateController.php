<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $data['timezones'] = timezone_identifiers_list();

        return view('user.profile', $data);
    }
}
