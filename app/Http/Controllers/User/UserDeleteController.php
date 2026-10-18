<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Http\Requests\UserDeleteRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserDeleteController extends MainController
{
    public function delete(UserDeleteRequest $request, int $id)
    {
        $user = User::query()
            ->unless(Auth::user()->hasRole('SuperAdmin'), fn ($query) => $query->where('company_id', Auth::user()->company_id))
            ->findOrFail($id);

        abort_if($user->hasRole('SuperAdmin') && ! Auth::user()->hasRole('SuperAdmin'), 403);

        $user->delete();

        return redirect('/user');
    }
}
