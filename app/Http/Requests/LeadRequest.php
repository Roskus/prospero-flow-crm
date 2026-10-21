<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Concerns\SanitizesInput;
use App\Models\Lead;
use App\Models\User;
use App\Rules\AddressRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Squire\Models\Country;

class LeadRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->can('create lead') ?? false;
    }

    public function messages(): array
    {
        return [
            'phone.max_digits' => 'Phone field should have :max_digits max digits.',
            'phone2.max_digits' => 'Phone2 field should have :max_digits max digits.',
            'mobile.max_digits' => 'Mobile field should have :max_digits max digits.',
            'seller_id.exists' => 'The seller does not exist.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $countryInput = $this->input('country_id');
        if (! empty($countryInput) && strlen($countryInput) > 2) {
            $normalizedCountryName = mb_strtolower(trim($countryInput));
            $country = Country::all()->first(fn (Country $country): bool => mb_strtolower($country->name) === $normalizedCountryName);
            if ($country) {
                $countryInput = $country->code_2;
            }
        }
        if (is_string($countryInput)) {
            $countryInput = strtolower(trim($countryInput));
        }

        $this->merge([
            'phone' => ! empty($this->input('phone')) ? preg_replace('/\D/', '', (string) $this->input('phone')) : null,
            'phone2' => ! empty($this->input('phone2')) ? preg_replace('/\D/', '', (string) $this->input('phone2')) : null,
            'mobile' => ! empty($this->input('mobile')) ? preg_replace('/\D/', '', (string) $this->input('mobile')) : null,
            'country_id' => $countryInput,
        ]);

        return [
            'name' => 'required|max:120',
            'business_name' => 'max:120',
            'dob' => 'nullable|date',
            'vat' => 'nullable|max:20',
            'phone' => 'nullable|string|max_digits:15',
            'phone2' => 'nullable|string|max_digits:15',
            'mobile' => 'nullable|string|max_digits:15',
            'email' => 'nullable|email|max:254',
            'email2' => 'nullable|email|max:254',
            'website' => 'nullable|url|max:255',
            'website_verified' => 'nullable|boolean',
            'linkedin' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'notes' => 'nullable',
            'seller_id' => [
                'required',
                'numeric',
                Rule::exists('user', 'id'),
                function ($attribute, $value, $fail) {
                    $seller = User::find($value);
                    $currentUser = Auth::user();
                    if ($seller && $seller->company_id !== $currentUser->company_id) {
                        $fail('The seller must belong to your company.');
                    }
                },
            ],
            ...AddressRules::rules(true),
            'schedule_contact' => 'nullable|date',
            'industry_id' => 'nullable|numeric',
            'opt_in' => 'nullable',
            'tags' => 'nullable',
            'status' => ['nullable', Rule::in([
                Lead::OPEN,
                Lead::IN_PROGRESS,
                Lead::WAITING_FEEDBACK,
                Lead::CONVERTED,
                Lead::CLOSED,
            ])],
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
            'deleted_at' => 'nullable|date',
        ];
    }
}
