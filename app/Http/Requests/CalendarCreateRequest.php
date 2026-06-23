<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CalendarCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('create calendar') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'is_all_day' => ['boolean'],
            'description' => ['nullable', 'string'],
            'meeting' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'guest_list' => ['nullable', 'array'],
        ];
    }
}
