<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Concerns\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EmailRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize()
    {
        return Auth::check();
    }

    protected function unsafeFields(): array
    {
        return ['body'];
    }

    public function rules()
    {
        return [
            'id' => 'nullable|exists:email,id',
            'from' => 'required|email',
            'subject' => 'required|string|max:255',
            'to' => 'nullable|email',
            'cc' => 'nullable|email',
            'bcc' => 'nullable|email',
            'body' => 'required|string',
            'signature' => 'nullable|in:0,1,true,false,on,off',
            'attachment.*' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:10240',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('signature')) {
            $this->merge([
                'signature' => filter_var($this->input('signature'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}
