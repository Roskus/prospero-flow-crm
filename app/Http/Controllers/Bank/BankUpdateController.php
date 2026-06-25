<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\MainController;
use App\Models\Bank;
use Illuminate\Http\Request;
use Squire\Models\Country;

class BankUpdateController extends MainController
{
    public function update(Request $request, string $uuid)
    {
        $bank = Bank::find($uuid);
        $data['bank'] = $bank;
        $data['countries'] = Country::all();

        return view('bank.bank', $data);
    }
}
