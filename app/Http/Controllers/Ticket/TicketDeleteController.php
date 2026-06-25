<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use App\Http\Requests\TicketDeleteRequest;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketDeleteController extends MainController
{
    public function delete(TicketDeleteRequest $request, int $id)
    {
        $ticket = Ticket::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();
        $ticket->delete();

        return redirect('/ticket');
    }
}
