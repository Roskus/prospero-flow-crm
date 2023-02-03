<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\Email;
use Illuminate\Http\Request;

class EmailViewController extends MainController
{
    public function view(Request $request, int $id)
    {
        $email = Email::find($id);
        $data['email'] = $email;

        return view('email.view', $data);
    }
}
