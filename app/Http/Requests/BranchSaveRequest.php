<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Branch;
use App\Rules\AddressRules;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BranchSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update company') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => is_string($this->input('code')) ? strtoupper(trim($this->input('code'))) : $this->input('code'),
            'country_id' => is_string($this->input('country_id')) ? strtolower($this->input('country_id')) : $this->input('country_id'),
        ]);
    }

    /**
     * The code stays unique among deleted branches too: it will be the invoice series of the branch.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Z0-9-]+$/',
                Rule::unique('branch', 'code')->where('company_id', Auth::user()->company_id)->ignore($this->integer('id') ?: null),
                function (string $attribute, mixed $value, Closure $fail): void {
                    $branch = $this->editedBranchWithInvoices();

                    if ($branch !== null && $value !== $branch->code) {
                        $fail(__('The code cannot be changed because it is the series of the branch invoices.'));
                    }
                },
            ],
            ...AddressRules::rules(),
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:191'],
        ];
    }

    private function editedBranchWithInvoices(): ?Branch
    {
        if (! $this->filled('id')) {
            return null;
        }

        $branch = Branch::ofCompany((int) Auth::user()->company_id)->find($this->integer('id'));

        return $branch?->hasInvoices() ? $branch : null;
    }

    public function messages(): array
    {
        return [
            'code.regex' => __('The code may only contain letters, numbers and dashes.'),
        ];
    }
}
