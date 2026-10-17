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
            'id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'is_all_day' => ['boolean'],
            'start_time' => ['nullable', 'required_unless:is_all_day,1', 'date_format:H:i'],
            'end_time' => ['nullable', 'required_unless:is_all_day,1', 'date_format:H:i', 'after:start_time'],
            'description' => ['nullable', 'string'],
            'meeting' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'guest_list' => ['nullable', 'array'],
            'guest_list.*' => ['string', 'max:255'],
        ];
    }
}
