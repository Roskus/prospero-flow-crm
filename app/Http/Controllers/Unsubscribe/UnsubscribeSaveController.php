<?php

declare(strict_types=1);

namespace App\Http\Controllers\Unsubscribe;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnsubscribeSaveRequest;
use App\Models\Customer;
use App\Models\Lead;

class UnsubscribeSaveController extends Controller
{
    public function save(UnsubscribeSaveRequest $request)
    {
        if (! empty($request->website)) {
            return redirect('/unsubscribe');
        }

        $email = $request->validated()['email'];

        $lead = Lead::where('email', $email)->first();
        if ($lead) {
            $lead->opt_in = 0;
            $lead->save();
        }

        $customer = Customer::where('email', $email)->first();
        if ($customer) {
            $customer->opt_in = 0;
            $customer->save();
        }

        return redirect('/unsubscribe')->with(['message' => __('From this moment you will not receive any more notifications from us')]);
    }
}
