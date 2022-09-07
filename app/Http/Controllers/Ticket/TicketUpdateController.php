<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $ticket = Ticket::find($id);
        $data['ticket'] = $ticket;
        return view('ticket.ticket', $data);
    }
}
