<?php

declare(strict_types=1);

namespace App\Http\Controllers\Unsubscribe;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class UnsubscribeSaveController extends Controller
{
    public function save(Request $request)
    {
        if ($request->email) {
            $lead = Lead::where('email', $request->email)->first();
            if ($lead) {
                $lead->opt_in = 0;
                $lead->save();
            }
        }

        return redirect('/unsubscribe')->with(['message' => __('From this moment you will not receive any more notifications from us')]);
    }
}
