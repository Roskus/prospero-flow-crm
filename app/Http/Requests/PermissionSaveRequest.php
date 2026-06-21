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

        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        if ($user->hasRole('CompanyAdmin')) {
            $roles = $this->input('roles', []);
            $superAdminRole = Role::where('name', 'SuperAdmin')->first();

            if ($superAdminRole && isset($roles[$superAdminRole->id])) {
                return false;
            }

            return true;
        }

        return false;
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
