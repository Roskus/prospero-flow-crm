<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\MainController;
use App\Http\Requests\BankDeleteRequest;
use App\Models\Bank;

class BankDeleteController extends MainController
{
    public function delete(BankDeleteRequest $request, string $uuid)
    {
        $bank = Bank::find($uuid);

        if (! $bank) {
            return redirect('bank')->with('error', __('Bank not found'));
        }

        $bank->delete();

        return redirect('bank')->with('success', __('Bank deleted successfully'));
    }
}
