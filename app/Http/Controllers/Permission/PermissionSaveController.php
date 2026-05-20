<?php

declare(strict_types=1);

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\MainController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class PermissionSaveController extends MainController
{
    public function save(Request $request): RedirectResponse
    {
        $roles = $request->roles;

        foreach ($roles as $role_id => $permissions) {
            Role::findById($role_id)->syncPermissions($permissions);
        }

        return redirect('/permission');
    }
}
