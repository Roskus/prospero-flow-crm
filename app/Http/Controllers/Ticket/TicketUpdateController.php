<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $user = new User();
        $ticket = Ticket::find($id);
        $data['ticket'] = $ticket;
        $data['users'] = $user->getAllActiveByCompany(Auth::user()->id);

        return view('ticket.ticket', $data);
    }
}
