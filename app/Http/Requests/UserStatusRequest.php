<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = User::find($this->route('id'));

        return $user !== null
            && Auth::user()->can('update user')
            && Auth::user()->canManageUser($user)
            && $user->id !== Auth::id();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'active.required' => __('The status is required.'),
            'active.boolean' => __('The status is not valid.'),
        ];
    }
}
