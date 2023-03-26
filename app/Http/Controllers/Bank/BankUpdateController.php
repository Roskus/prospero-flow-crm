<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use App\Models\Bank;
use Illuminate\Http\Request;
use Squire\Models\Country;

class BankUpdateController
{
    public function update(Request $request, int $id)
    {
        $bank = Bank::find($id);
        $data['bank'] = $bank;
        $data['countries'] = Country::all();

        return view('bank.bank', $data);
    }
}
