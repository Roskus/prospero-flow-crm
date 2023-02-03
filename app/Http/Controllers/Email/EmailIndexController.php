<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailIndexController extends MainController
{
    public function index(Request $request)
    {
        $data['emails'] = (new Email())->getAllByCompanyId(
            Auth::user()->company_id,
            $request->search
        );

        return view('email.index', $data);
    }
}
