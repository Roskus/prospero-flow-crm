<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\BankCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BankCardDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! Auth::user()?->can('delete accounting')) {
            return false;
        }

        $bankCard = BankCard::find($this->route('id'));

        return $bankCard && $bankCard->company_id === Auth::user()->company_id;
    }

    public function rules(): array
    {
        return [];
    }
}
