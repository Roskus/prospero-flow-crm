<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketRepository
{
    public function save(array $data): ?Ticket
    {
        if (empty($data['id'])) {
            $ticket = new Ticket();
            $ticket->created_at = now();
            $ticket->created_by = Auth::user()->id;
        } else {
            $ticket = Ticket::find($data['id']);
        }
        $ticket->company_id = Auth::user()->company_id;
        $ticket->title = $data['title'];
        $ticket->customer_id = ! empty($data['customer_id']) ? $data['customer_id'] : null;
        $ticket->priority = ! empty($data['priority']) ? $data['priority'] : null;
        $ticket->description = $data['description'];
        $ticket->assigned_to = ! empty($data['assigned_to']) ? $data['assigned_to'] : null;
        $ticket->type = ! empty($data['type']) ? $data['type'] : null;
        $ticket->order_id = ! empty($data['order_id']) ? $data['order_id'] : null;
        $ticket->status = ! empty($data['status']) ? $data['status'] : 'new';
        $ticket->updated_at = now();
        $ticket->save();

        return $ticket;
    }
}
