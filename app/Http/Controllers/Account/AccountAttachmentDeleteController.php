<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AccountAttachmentDeleteController
{
    public function delete(Request $request, int $id): RedirectResponse
    {
        $account = Account::where('company_id', Auth::user()->company_id)->findOrFail($id);

        if ($account->attachment) {
            Storage::disk('public')->delete($account->attachment);
            $account->attachment = null;
            $account->save();
        }

        return redirect("/account/edit/{$id}");
    }
}
