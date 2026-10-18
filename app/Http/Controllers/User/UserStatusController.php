<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Http\Requests\UserStatusRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class UserStatusController extends MainController
{
    public function update(UserStatusRequest $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->active = $request->boolean('active');
        $user->save();

        return redirect('/user')->with([
            'status' => 'success',
            'message' => $user->active ? __('User activated') : __('User deactivated'),
        ]);
    }
}
