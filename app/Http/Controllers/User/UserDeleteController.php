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
        $user = User::where('company_id', Auth::user()->company_id)->find($id);

        if ($user) {
            $user->delete();
        }

        return redirect('/user');
    }
}
