<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Message as CustomerMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerCreateMessageController extends Controller
{
    public function save(Request $request)
    {
        $message = new CustomerMessage();
        $message->fill([
            'customer_id' => $request->customer_id,
            'body' => $request->message,
            'author_id' => Auth::user()->id,
        ]);
        $message->save();

        return redirect("customer/update/$request->customer_id");
    }
}
