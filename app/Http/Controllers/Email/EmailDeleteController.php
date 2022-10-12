<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\Email;
use Illuminate\Http\Request;


class EmailDeleteController extends MainController
{
    public function delete(Request $request, int $id)
    {
        $email = Email::find($id);
        $email->delete();

        return redirect('/email')->with(['status' => true, 'message' => __('Email deleted successfully')]);
    }
}
