<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AssetAssignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update asset') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('user', 'id')
                    ->where('company_id', Auth::user()->company_id)
                    ->where('active', true)
                    ->whereNull('deleted_at'),
            ],
            'start_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => __('Select the user who receives the asset.'),
            'user_id.exists' => __('The selected user is not valid.'),
        ];
    }
}
