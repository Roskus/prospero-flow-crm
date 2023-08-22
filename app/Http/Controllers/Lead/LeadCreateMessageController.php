<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Models\Lead\Message as LeadMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadCreateMessageController extends Controller
{
    public function save(Request $request)
    {
        $message = new LeadMessage();
        $message->fill([
            'lead_id' => $request->lead_id,
            'body' => $request->message,
            'author_id' => Auth::user()->id,
        ]);
        $message->save();

        return back();
    }
}
