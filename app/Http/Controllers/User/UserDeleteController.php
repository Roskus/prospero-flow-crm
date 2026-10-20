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
            ->manageableBy(Auth::user())
            ->findOrFail($id);

        abort_unless(Auth::user()->canManageUser($user), 403);

        $user->delete();

        return redirect('/user');
    }
}
