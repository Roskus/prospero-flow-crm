<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use App\Models\Customer\Message as CustomerMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerCreateMessageController extends MainController
{
    public function save(Request $request): RedirectResponse
    {
        Customer::where('id', $request->customer_id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        $message = new CustomerMessage;
        $message->fill([
            'customer_id' => $request->customer_id,
            'body' => $request->message,
            'author_id' => Auth::user()->id,
        ]);
        $message->save();

        return back();
    }
}
