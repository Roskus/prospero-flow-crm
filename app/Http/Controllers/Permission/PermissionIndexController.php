<?php

declare(strict_types=1);

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionIndexController extends MainController
{
    public function index(Request $request)
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('permissions.index', compact('roles', 'permissions'));
    }
}
