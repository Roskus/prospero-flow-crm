<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BankAccountDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('delete accounting') ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
