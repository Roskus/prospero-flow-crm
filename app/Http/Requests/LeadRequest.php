<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    public function messages(): array
    {
        return [
            'phone.max_digits' => 'Phone field should have :max_digits max digits.',
            'phone2.max_digits' => 'Phone2 field should have :max_digits max digits.',
            'mobile.max_digits' => 'Mobile field should have :max_digits max digits.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $this->merge([
            'phone' => ! empty($this->input('phone')) ? str_replace(['+', '-', '', '(', ')', '.'], '', $this->input('phone')) : null,
            'phone2' => ! empty($this->input('phone2')) ? str_replace(['+', '-', '', '(', ')', '.'], '', $this->input('phone2')) : null,
            'mobile' => ! empty($this->input('mobile')) ? str_replace(['+', '-', '', '(', ')', '.'], '', $this->input('mobile')) : null,
        ]);

        return [
            'name' => 'required|max:80',
            'business_name' => 'max:80',
            'dob' => 'nullable|date',
            'vat' => 'nullable|max:20',
            'phone' => 'nullable|string|max_digits:15',
            'phone2' => 'nullable|string|max_digits:15',
            'mobile' => 'nullable|string|max_digits:15',
            'email' => 'nullable|email|max:254',
            'email2' => 'nullable|email|max:254',
            'website' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'notes' => 'nullable',
            'seller_id' => 'required|numeric',
            'country_id' => 'required|max:2',
            'province' => 'nullable|max:80',
            'city' => 'nullable|max:50',
            'locality' => 'nullable|max:80',
            'street' => 'nullable|max:80',
            'zipcode' => 'nullable|max:10',
            'schedule_contact' => 'nullable|date',
            'industry_id' => 'nullable|numeric',
            'latitude' => 'nullable|numeric|gte:-90|lte:90|required_with:longitude',
            'longitude' => 'nullable|numeric|gte:-180|lte:180|required_with:latitude',
            'opt_in' => 'nullable',
            'tags' => 'nullable',
            'status' => 'nullable', //@todo validate status options
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
            'deleted_at' => 'nullable|date',
        ];
    }
}
