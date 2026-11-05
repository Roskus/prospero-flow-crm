<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Query of the paginated lead and customer API lists; access is checked by the route permissions.
 */
class ProspectListRequest extends FormRequest
{
    public const int SEARCH_MAX_LENGTH = 120;

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
            'search' => ['nullable', 'string', 'max:'.self::SEARCH_MAX_LENGTH],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'search.string' => __('The search text is not valid.'),
            'search.max' => __('The search text may not be longer than :max characters.'),
            'per_page.integer' => __('The number of results per page must be a number.'),
            'page.integer' => __('The page must be a number.'),
            'page.min' => __('The page must be a number.'),
        ];
    }
}
