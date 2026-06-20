<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->hasRole('SuperAdmin') ?? false;
    }

    public function rules(): array
    {
        return [
            'roles' => 'required|array',
            'roles.*' => 'required|array',
            'roles.*.*' => 'required|string|exists:permission,name',
        ];
    }

    public function prepareForValidation(): void
    {
        $roles = $this->input('roles', []);
        foreach ($roles as $role_id => $permissions) {
            if (! Role::where('id', $role_id)->exists()) {
                $this->merge(['invalid_role' => $role_id]);
            }
        }
    }
}
