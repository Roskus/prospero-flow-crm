<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketIndexController extends MainController
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $userId = Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('CompanyAdmin') || Auth::user()->hasRole('Support') ? null : Auth::user()->id;

        $ticket = new Ticket;

        $data['tickets'] = $ticket->getAllByCompanyId((int) Auth::user()->company_id, $search, $userId);
        $data['search'] = $search;
        $data['customers'] = Customer::whereCompany(Auth::user()->company);

        return view('ticket.index', $data);
    }
}
