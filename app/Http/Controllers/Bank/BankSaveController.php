<?php

namespace App\Http\Controllers\Bank;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankSaveController
{
    public function save(Request $request)
    {
        if (empty($request->id)) {
            $bank = new Bank();
            $bank->created_at = now();
        } else {
            $bank = Bank::find($request->id);
        }
        $bank->name = $request->name;
        $bank->country = $request->country;
        $bank->phone = $request->phone;
        $bank->email = $request->email;
        $bank->website = $request->website;
        $bank->save();

        return redirect('bank');
    }
}
