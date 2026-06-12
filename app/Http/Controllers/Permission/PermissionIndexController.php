<?php

declare(strict_types=1);

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionIndexController extends MainController
{
    public function index(Request $request)
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        $actionOrder = [
            'create' => 1,
            'read' => 2,
            'update' => 3,
            'delete' => 4,
            'export' => 5,
        ];

        $permissionGroups = $permissions
            ->map(function (Permission $permission): array {
                $action = Str::before($permission->name, ' ');
                $resource = Str::after($permission->name, ' ');

                if ($resource === $permission->name) {
                    $resource = $permission->name;
                }

                return [
                    'permission' => $permission,
                    'action' => $action,
                    'resource' => $resource,
                ];
            })
            ->groupBy('resource')
            ->map(function ($group) use ($actionOrder) {
                return collect($group)
                    ->sortBy(fn (array $item) => $actionOrder[$item['action']] ?? 99)
                    ->values();
            })
            ->sortKeys();

        return view('permissions.index', compact('roles', 'permissionGroups'));
    }
}
