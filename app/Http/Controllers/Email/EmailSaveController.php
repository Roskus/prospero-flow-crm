<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailSaveController extends MainController
{
    public function save(Request $request)
    {
        if (empty($request->id)) {
            $email = new Email();
            $email->created_at = now();
        } else {
            $email = Email::find($request->id);
        }
        $email->from = $request->from;
        $email->company_id = Auth::user()->company_id;
        $email->subject = $request->subject;
        $email->to = $request->to;
        $email->cc = $request->cc;
        $email->body = $request->body;
        $email->signature = (isset($request->signature)) ? isset($request->signature) : false;
        $email->updated_at = now();
        $email->status = Email::DRAFT;
        $email->save();

        return redirect('/email');
    }
}
