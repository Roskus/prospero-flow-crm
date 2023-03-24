<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use App\Models\Bank;

class BankCreateController
{
    public function create()
    {
        $bank = new Bank();
        $data['bank'] = $bank;
        return view('bank.bank', $data);
    }
}
