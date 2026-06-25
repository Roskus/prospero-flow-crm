<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\MainController;
use App\Http\Requests\BankSaveRequest;
use App\Models\Bank;
use Ramsey\Uuid\Uuid;

class BankSaveController extends MainController
{
    public function save(BankSaveRequest $request)
    {
        $validated = $request->validated();

        if (empty($validated['uuid'])) {
            $bank = new Bank;
            $bank->created_at = now();
            $bank->uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $validated['bic'])->toString();
        } else {
            $bank = Bank::find($validated['uuid']);
        }
        $bank->name = $validated['name'];
        $bank->phone = $validated['phone'] ?? null;
        $bank->bic = $validated['bic'];
        $bank->country_id = strtolower($validated['country_id']);
        $bank->email = $validated['email'] ?? null;
        $bank->website = $validated['website'] ?? null;
        $bank->updated_at = now();
        $bank->save();

        return redirect('bank');
    }
}
