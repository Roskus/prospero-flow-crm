<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class PermissionSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        $authorized = $user->hasRole('SuperAdmin');

        if (! $authorized && $user->hasRole('CompanyAdmin')) {
            $roles = $this->input('roles', []);
            $superAdminRole = Role::where('name', 'SuperAdmin')->first();
            $authorized = ! ($superAdminRole && isset($roles[$superAdminRole->id]));
        }

        return $authorized;
    }

    public function rules(): array
    {
        return [
            'roles' => 'required|array',
            'roles.*' => 'required|array',
            'roles.*.*' => 'required|integer|exists:permission,id',
        ];
    }
}
