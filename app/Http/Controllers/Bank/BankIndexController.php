<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use App\Models\Bank;

class BankIndexController
{
    public function index()
    {
        $bank = new Bank();
        $data['banks'] = $bank->getAll();
        return view('bank.index', $data);
    }
}
