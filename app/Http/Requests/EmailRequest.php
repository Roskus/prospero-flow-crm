<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EmailRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'from' => 'required|email',
            'subject' => 'required|string|max:255',
            'to' => 'nullable|email',
            'cc' => 'nullable|email',
            'body' => 'nullable|string',
            //'signature' => 'nullable|boolean',
            'attachment.*' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:10240', //10 MB
        ];
    }
}
