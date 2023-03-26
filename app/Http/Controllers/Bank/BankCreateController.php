<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use App\Models\Bank;
use Squire\Models\Country;

class BankCreateController
{
    public function create()
    {
        $bank = new Bank();
        $data['bank'] = $bank;
        $data['countries'] = Country::all();

        return view('bank.bank', $data);
    }
}
