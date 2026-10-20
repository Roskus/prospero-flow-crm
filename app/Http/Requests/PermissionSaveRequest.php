<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
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

        if (! $authorized && $user->isCompanyAdmin()) {
            $roles = $this->input('roles', []);
            $authorized = Role::query()
                ->whereIn('name', ['SuperAdmin', User::MULTI_COMPANY_ADMIN])
                ->pluck('id')
                ->every(fn (int $roleId): bool => ! isset($roles[$roleId]));
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
