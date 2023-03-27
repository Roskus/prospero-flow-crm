<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use App\Repositories\TicketRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

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

        if ($request->hasFile('attachment')) {
            $attachments = $request->file('attachment');
            foreach ($attachments as $attachment) {
                if ($attachment->isValid()) {
                    Validator::validate(['attachment' => $attachment], [
                        'attachment' => File::types(['image/*', 'application/pdf'])
                            ->max(2 * 1024),
                    ]);

                    $attachment->storeAs(
                        'attachments-tickets'.DIRECTORY_SEPARATOR.$ticket->id,
                        $attachment->getClientOriginalName(),
                        'public'
                    );
                }
            }
        }

        return redirect('/ticket');
    }
}
