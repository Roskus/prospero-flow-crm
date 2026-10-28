<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesInvoicePayment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InvoicePaymentRequest extends FormRequest
{
    use ValidatesInvoicePayment;

    public function authorize(): bool
    {
        return Auth::user()?->can('create payment') ?? false;
    }

    public function rules(): array
    {
        return $this->paymentRules();
    }
}
