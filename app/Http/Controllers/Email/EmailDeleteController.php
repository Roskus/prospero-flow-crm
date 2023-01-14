<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailDeleteController extends MainController
{
    public function delete(Request $request, int $id)
    {
        $email = Email::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $email->delete();

        return redirect('/email')->with(['status' => true, 'message' => __('Email deleted successfully')]);
    }
}
