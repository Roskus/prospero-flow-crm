<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank\Account;

use App\Http\Controllers\MainController;
use App\Http\Requests\BankAccountSaveRequest;
use App\Models\Bank\Account as BankAccount;
use Illuminate\Support\Facades\Auth;

class BankAccountSaveController extends MainController
{
    public function save(BankAccountSaveRequest $request)
    {
        $validated = $request->validated();

        $bankAccount = empty($validated['id'] ?? null)
            ? new BankAccount
            : BankAccount::where('company_id', Auth::user()->company_id)->findOrFail($validated['id']);

        $bankAccount->company_id = Auth::user()->company_id;
        $bankAccount->fill(array_merge(
            $validated,
            ['country_id' => strtolower($validated['country_id'])]
        ));
        $bankAccount->save();

        return redirect('/bank-account');
    }
}
