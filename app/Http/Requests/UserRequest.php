<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (empty($this->input('id'))) {
            return Auth::user()->can('create user');
        }

        $user = User::find($this->input('id'));

        return $user !== null && Auth::user()->can('update user') && Auth::user()->canManageUser($user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:15',
            'photo' => 'nullable|string|max:255',
            'lang' => 'required|string|max:2',
            'timezone' => 'nullable|timezone',
            'password' => ['required_without:id', 'nullable', 'string', 'min:8', 'confirmed'], // Required on create, optional on update
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', Rule::in(Auth::user()->assignableRoles()->pluck('name')->all())],
            'extra_companies' => Auth::user()->hasRole('SuperAdmin') ? ['array'] : 'prohibited',
            'extra_companies.*' => ['nullable', 'integer', Rule::exists('company', 'id')->whereNull('deleted_at')],
            'default_branch_id' => ['nullable', 'integer', Rule::exists('branch', 'id')->where('company_id', $this->defaultCompanyId())->whereNull('deleted_at')],
        ];
    }

    /**
     * The company the saved user will belong to: the one chosen by a SuperAdmin, the edited user's default company
     * or, for a new user, the creator's active company.
     */
    public function defaultCompanyId(): int
    {
        if (Auth::user()->hasRole('SuperAdmin') && $this->filled('company_id')) {
            return $this->integer('company_id');
        }

        if ($this->filled('id')) {
            return (int) User::query()->manageableBy(Auth::user())->find($this->integer('id'))?->defaultCompanyId();
        }

        return (int) Auth::user()->company_id;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'roles.*.in' => __('You are not allowed to assign this role.'),
            'password.required_without' => __('The password is required for new users.'),
            'password.confirmed' => __('The password confirmation does not match.'),
            'extra_companies.prohibited' => __('You are not allowed to change the companies of a user.'),
        ];
    }
}
