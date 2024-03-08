<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\MainController;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankDeleteController extends MainController
{
    public function delete(Request $request, string $uuid)
    {
        $bank = Bank::find($uuid);
        //@TODO improve this security check
        if (Auth::user()->company_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $bank->delete();

        return redirect('bank');
    }
}
