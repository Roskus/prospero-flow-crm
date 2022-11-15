<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketCreateController extends MainController
{
    public function create(Request $request)
    {
        $ticket = new Ticket;
        $user = new User();
        $data['ticket'] = $ticket;
        $data['users'] = $user->getAllActiveByCompany(Auth::user()->id);

        return view('ticket.ticket', $data);
    }
}
