<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BrandUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update brand') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'max:80'],
        ];
    }
}
