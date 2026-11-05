<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Squire\Models\Country;
use Squire\Repository;

/**
 * Query of the country API list: the language of the names, one of the languages the
 * countries library ships.
 */
class CountryListRequest extends FormRequest
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
            'lang' => ['nullable', 'string', Rule::in(self::languages())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lang.in' => __('The language must be one of: :languages.', ['languages' => implode(', ', self::languages())]),
        ];
    }

    /**
     * Requested language, or the application locale when the library has it, or English.
     */
    public function language(): string
    {
        $language = $this->validated('lang') ?? app()->getLocale();

        return in_array($language, self::languages(), true) ? $language : 'en';
    }

    /**
     * @return list<string>
     */
    public static function languages(): array
    {
        return array_keys(Repository::getSources(Country::class));
    }
}
