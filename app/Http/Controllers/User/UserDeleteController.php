<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\User;

class UserDeleteController extends MainController
{
    public function delete(int $id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect('/user');
    }
}
