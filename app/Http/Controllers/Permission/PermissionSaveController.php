<?php

declare(strict_types=1);

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\MainController;
use App\Http\Requests\PermissionSaveRequest;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;

class PermissionSaveController extends MainController
{
    public function save(PermissionSaveRequest $request): RedirectResponse
    {
        foreach ($request->validated()['roles'] as $role_id => $permissions) {
            $role = Role::find($role_id);
            if ($role) {
                $role->syncPermissions($permissions);
            }
        }

        return redirect('/permission')->with('success', __('Permissions updated successfully'));
    }
}
