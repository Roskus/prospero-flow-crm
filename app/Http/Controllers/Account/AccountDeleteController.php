<?php declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountDeleteController
{
    public function delete(Request $request, int $id)
    {
        if (Auth::user()->cannot('account delete')) {
            return redirect()->back()->with('error', 'You do not have permission to delete accounts.');
        }

        $account = Account::find($id);
        if ($account) {
            $account->delete();
        }

        return redirect()->back();
    }
}
