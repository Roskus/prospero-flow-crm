<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $email = Email::where('company_id', Auth::user()->company_id)->find($id);
        $data['froms'] = [
            ['email' => Auth::user()->company->email, 'name' => Auth::user()->company->name],
            ['email' => Auth::user()->email, 'name' => Auth::user()->first_name.' '.Auth::user()->last_name],
        ];
        $data['email'] = $email;

        return view('email.email', $data);
    }
}
