<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank\Account;

use App\Http\Controllers\MainController;
use App\Models\Bank\Account as BankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountDeleteController extends MainController
{
    public function delete(Request $request, int $id): RedirectResponse
    {
        $bankAccount = BankAccount::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        $bankAccount->delete();

        return redirect('/bank-account');
    }
}
