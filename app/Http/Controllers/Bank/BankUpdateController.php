<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use Illuminate\Http\Request;
use App\Models\Bank;

class BankUpdateController
{
    public function update(Request $request, int $id)
    {
        $bank = Bank::find($id);
        $data['bank'] = $bank;
        return view('bank.bank', $data);
    }
}
