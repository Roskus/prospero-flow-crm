<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Bank\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BankAccountDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! Auth::user()?->can('delete accounting')) {
            return false;
        }

        $account = Account::find($this->route('id'));

        return $account && $account->company_id === Auth::user()->company_id;
    }

    public function rules(): array
    {
        return [];
    }
}
