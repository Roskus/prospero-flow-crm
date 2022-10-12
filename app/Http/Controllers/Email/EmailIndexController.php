<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailIndexController extends MainController
{
    public function index(Request $request)
    {
        $email = new Email();
        $data['emails'] = $email->getAllByCompanyId(Auth::user()->company_id);

        return view('email.index', $data);
    }
}
