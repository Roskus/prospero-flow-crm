<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use App\Models\Bank;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class BankSaveController
{
    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'country_id' => 'required',
            'bic' => empty($request->uuid) ? 'required|bic|unique:bank' : 'required|bic',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
        ]);

        if (empty($request->uuid)) {
            $bank = new Bank;
            $bank->created_at = now();
        } else {
            $bank = Bank::find($request->uuid);
        }
        $bank->uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $request->bic)->toString();
        $bank->name = $request->name;
        $bank->phone = $request->phone;
        $bank->bic = $request->bic;
        $bank->country_id = $request->country_id;
        $bank->email = $request->email;
        $bank->website = $request->website;
        $bank->updated_at = now();
        $bank->save();

        return redirect('bank');
    }
}
