<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $data['roles'] = Auth::user()->assignableRoles();
        $data['companies'] = Auth::user()->hasRole('SuperAdmin') ? Company::all() : [];
        $data['timezones'] = timezone_identifiers_list();
        $data['branches'] = Branch::query()
            ->whereIn('company_id', Auth::user()->hasRole('SuperAdmin') ? $data['companies']->pluck('id') : [Auth::user()->company_id])
            ->orderBy('company_id')->orderByDesc('is_main')->orderBy('code')
            ->get();

        return view('user.user', $data);
    }
}
