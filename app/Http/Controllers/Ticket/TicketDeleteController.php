<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketDeleteController extends MainController
{
    public function delete(Request $request, int $id)
    {
        $ticket = Ticket::find($id);
        $ticket->delete();

        return redirect('/ticket');
    }
}
