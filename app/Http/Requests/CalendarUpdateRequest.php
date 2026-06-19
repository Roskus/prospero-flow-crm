<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CalendarUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update calendar') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'description' => ['sometimes', 'string'],
            'end_date' => ['sometimes', 'date'],
        ];
    }
}
