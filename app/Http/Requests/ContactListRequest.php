<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\ValueObjects\LinkedinProfileUrl;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Query of the contact API list; access is checked by the route permissions.
 */
class ContactListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'linkedin' => [
                'nullable',
                'string',
                'max:255',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (LinkedinProfileUrl::fromString($value) === null) {
                        $fail(__('The LinkedIn URL must be a LinkedIn profile URL.'));
                    }
                },
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'linkedin.string' => __('The LinkedIn URL must be a LinkedIn profile URL.'),
            'linkedin.max' => __('The LinkedIn URL may not be longer than :max characters.'),
        ];
    }

    public function linkedinProfile(): ?LinkedinProfileUrl
    {
        return LinkedinProfileUrl::fromString($this->validated('linkedin'));
    }
}
