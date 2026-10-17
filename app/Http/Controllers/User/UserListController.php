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
            ->unless(Auth::user()->hasRole('SuperAdmin'), fn ($query) => $query->where('company_id', Auth::user()->company_id))
            ->orderBy('company_id')
            ->paginate(10);
        $data['users'] = $users;

        return view('user.index', $data);
    }
}
