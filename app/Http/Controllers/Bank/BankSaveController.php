<?php

declare(strict_types=1);

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
        $bank->country_id = $request->country_id;
        $bank->phone = $request->phone;
        $bank->email = $request->email;
        $bank->website = $request->website;
        $bank->updated_at = now();
        $bank->save();

        return redirect('bank');
    }
}
