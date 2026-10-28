<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CampaignSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->can($this->filled('id') ? 'update campaign' : 'create campaign');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'from' => ['required', 'email', Rule::in(array_column(Campaign::fromAddresses(Auth::user()), 'email'))],
            'body' => ['required', 'string'],
            'tags' => ['nullable', 'string'],
            'schedule_send_date' => ['nullable', 'date_format:Y-m-d'],
            'schedule_send_time' => ['nullable', 'date_format:H:i,H:i:s'],
        ];
    }

    public function messages(): array
    {
        return [
            'from.in' => __('The from address must be the company or your own email.'),
        ];
    }
}
