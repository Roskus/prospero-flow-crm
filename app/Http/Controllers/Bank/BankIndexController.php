<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\MainController;
use App\Models\Bank;
use Illuminate\Http\Request;
use Squire\Models\Country;

class BankIndexController extends MainController
{
    public function index(Request $request)
    {
        $filters = [];
        if ($request->filled('country_id')) {
            $filters['country_id'] = strtolower($request->country_id);
        }
        if ($request->filled('name')) {
            $filters['name'] = $request->name;
        }

        $bank = new Bank;
        $data['banks'] = $bank->getAllPaginated($filters, 20);
        $data['countries'] = Country::all();

        return view('bank.index', $data);
    }
}
