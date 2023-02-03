<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketIndexController extends MainController
{
    public function index(Request $request)
    {
        $ticket = new Ticket();
        $data['tickets'] = $ticket->getLatestByCompany(Auth::user()->company_id);

        return view('ticket.index', $data);
    }
}
