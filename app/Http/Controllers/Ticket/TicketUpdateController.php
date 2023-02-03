<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use App\Models\Customer;
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
        $customer = new Customer();
        $data['ticket'] = $ticket;
        $data['users'] = $user->getAllActiveByCompany(Auth::user()->company_id);
        $data['customers'] = $customer->getAllByCompanyId(Auth::user()->company_id);

        return view('ticket.ticket', $data);
    }
}
