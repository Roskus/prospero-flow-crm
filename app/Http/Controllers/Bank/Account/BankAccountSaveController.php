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

        $bankAccount = empty($request->id)
            ? new BankAccount
            : BankAccount::where('company_id', Auth::user()->company_id)->findOrFail($request->id);

        if ($bankAccount->isBranchCashAccount()) {
            $validated['type'] = BankAccount::TYPE_CASH;
        }

        $isCash = $validated['type'] === BankAccount::TYPE_CASH;

        $bankAccount->company_id = Auth::user()->company_id;
        $bankAccount->fill(array_merge($validated, [
            'country_id' => strtolower((string) (($validated['country_id'] ?? null) ?: Auth::user()->company->country_id ?: 'es')),
            'show_on_invoice' => ! $isCash && $request->boolean('show_on_invoice'),
        ]));
        $bankAccount->save();

        return redirect('/bank-account');
    }
}
