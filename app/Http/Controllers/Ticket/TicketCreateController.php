<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketCreateController extends MainController
{
    public function create(Request $request)
    {
        $ticket = new Ticket;
        $data['ticket'] = $ticket;
        return view('ticket.ticket', $data);
    }
}
