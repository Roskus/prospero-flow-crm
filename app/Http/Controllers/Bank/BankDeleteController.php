<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\MainController;
use App\Models\Bank;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankDeleteController extends MainController
{
    public function delete(Request $request, string $uuid)
    {
        $bank = Bank::find($uuid);
        // @TODO improve this security check
        if ((int) Auth::user()->company_id !== Company::DEFAULT_COMPANY) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $bank->delete();

        return redirect('bank');
    }
}
