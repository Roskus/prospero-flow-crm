<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EmailReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('read email') ?? false;
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
