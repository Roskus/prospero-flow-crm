<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Ticket\Message as TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketCreateMessageController extends Controller
{
    public function save(Request $request)
    {
        $message = new TicketMessage();
        $message->fill([
            'author_id' => Auth::user()->id,
            'ticket_id' => $request->ticket_id,
            'body' => $request->message,
        ]);
        $message->save();

        return redirect("ticket/update/$request->ticket_id");
    }
}
