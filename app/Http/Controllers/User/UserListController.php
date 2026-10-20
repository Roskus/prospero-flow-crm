<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserListController extends MainController
{
    public function index(Request $request)
    {
        $users = User::query()
            ->with(['company' => fn ($query) => $query->withTrashed()])
            ->manageableBy(Auth::user())
            ->orderBy('company_id')
            ->paginate(10);
        $data['users'] = $users;

        if (Auth::user()->hasRole('SuperAdmin')) {
            $data['multiCompanyUsersCount'] = User::query()->multiCompany()->count();
            $data['multiCompanyAdminsCount'] = User::query()
                ->whereHas('roles', fn ($query) => $query->where('name', User::MULTI_COMPANY_ADMIN))
                ->count();
        }

        return view('user.index', $data);
    }
}
