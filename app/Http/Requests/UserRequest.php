<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasRole('SuperAdmin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
            //'timezone' => 'required|string|max:255',
            'password' => 'nullable|string|min:8', // Optional, only if user wants to update password
        ];
    }
}
