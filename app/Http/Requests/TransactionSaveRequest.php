<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Concerns\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TransactionSaveRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Require the 'update accounting' permission to modify an existing transaction
        if ($this->filled('id')) {
            return $user->can('update accounting');
        }

        // Require the 'create accounting' permission to create a new transaction
        return $user->can('create accounting');
    }

    public function rules(): array
    {
        /*
         * Negative amounts (refunds, corrections) can corrupt the accounting balance.
         * Only SuperAdmin and CompanyAdmin are trusted to enter negative values.
         * All other roles are restricted to positive amounts (min:0).
         */
        $amountRule = Auth::user()?->hasRole(['SuperAdmin', 'CompanyAdmin'])
            ? ['required', 'numeric']
            : ['required', 'numeric', 'min:0'];

        return [
            'name' => ['required', 'string', 'max:80'],
            'type' => ['required', 'in:income,expense'],
            'amount' => $amountRule,
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'payment_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,paid,overdue'],
            'transaction_category_id' => [
                'nullable',
                'integer',
                Rule::exists('transaction_category', 'id')->where('company_id', $this->user()?->company_id),
            ],
            'bank_account_id' => [
                'nullable',
                'integer',
                Rule::exists('bank_account', 'id')->where('company_id', $this->user()?->company_id),
            ],
            'bank_card_id' => [
                'nullable',
                'integer',
                Rule::exists('bank_card', 'id')->where('company_id', $this->user()?->company_id),
            ],
            'reference' => ['nullable', 'string', 'max:80'],
            'customer_id' => [
                'nullable',
                'integer',
                Rule::exists('customer', 'id')->where('company_id', $this->user()?->company_id),
            ],
            'supplier_id' => [
                'nullable',
                'integer',
                Rule::exists('supplier', 'id')->where('company_id', $this->user()?->company_id),
            ],
            'notes' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx'],
        ];
    }
}
