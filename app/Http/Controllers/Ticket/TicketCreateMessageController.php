<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use App\Models\Ticket;
use App\Models\Ticket\Message as TicketMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketCreateMessageController extends MainController
{
    public function save(Request $request): RedirectResponse
    {
        Ticket::where('id', $request->ticket_id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        $message = new TicketMessage;
        $message->fill([
            'author_id' => Auth::user()->id,
            'ticket_id' => $request->ticket_id,
            'body' => $request->message,
        ]);
        $message->save();

        return redirect("ticket/update/$request->ticket_id");
    }
}
