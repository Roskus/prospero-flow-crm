<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketSaveController extends MainController
{
    public function save(Request $request)
    {
        if ($request->id) {
            $ticket = Ticket::find($request->id);
        } else {
            $ticket = new Ticket();
            $ticket->created_at = now();
            $ticket->created_by = Auth::user()->id;
        }
        $ticket->company_id = Auth::user()->company_id;
        $ticket->title = $request->title;
        $ticket->customer_id = $request->customer_id;
        $ticket->priority = $request->priority;
        $ticket->description = $request->description;
        $ticket->assigned_to = $request->assigned_to;
        $ticket->type = $request->type;
        $ticket->status = $request->status;
        $ticket->updated_at = now();
        $ticket->save();

        return redirect('/ticket');
    }
}
