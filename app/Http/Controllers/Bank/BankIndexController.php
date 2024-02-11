<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use App\Models\Bank;
use Illuminate\Http\Request;
use Squire\Models\Country;

class BankIndexController
{
    public function index(Request $request)
    {
        $filters = [];
        $bank = new Bank();
        if (isset($request->country_id)) {
            $filters['country_id'] = $request->country_id;
        }
        $data['banks'] = $bank->getAllPaginated($filters, 20);
        $data['countries'] = Country::all();

        return view('bank.index', $data);
    }
}
