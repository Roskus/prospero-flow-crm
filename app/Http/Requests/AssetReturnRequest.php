<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AssetReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update asset') ?? false;
    }

    /**
     * The end date is checked against the assignment start date when returning.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'end_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
