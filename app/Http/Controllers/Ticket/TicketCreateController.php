<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketCreateController extends MainController
{
    public function create(Request $request)
    {
        $ticket = new Ticket;
        $user = new User;
        $customer = new Customer;
        $data['ticket'] = $ticket;
        $data['users'] = $user->getAllActiveByCompany((int) Auth::user()->company_id);
        $data['customers'] = $customer->getAllByCompanyId((int) Auth::user()->company_id);

        return view('ticket.ticket', $data);
    }
}
