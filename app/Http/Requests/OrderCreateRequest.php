<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('create order') ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'max:3'],
            'status' => ['sometimes', 'integer', 'in:0,1,2,3'],
        ];
    }
}
