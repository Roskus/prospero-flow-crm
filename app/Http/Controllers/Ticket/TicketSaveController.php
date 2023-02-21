<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use App\Repositories\TicketRepository;
use Illuminate\Http\Request;

class TicketSaveController extends MainController
{
    private TicketRepository $ticketRepository;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function save(Request $request)
    {
        $ticket = $this->ticketRepository->save($request->all());

        return redirect('/ticket');
    }
}
