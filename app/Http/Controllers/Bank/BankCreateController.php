<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\MainController;
use App\Models\Bank;
use Squire\Models\Country;

class BankCreateController extends MainController
{
    public function create()
    {
        $bank = new Bank;
        $data['bank'] = $bank;
        $data['countries'] = Country::all();

        return view('bank.bank', $data);
    }
}
