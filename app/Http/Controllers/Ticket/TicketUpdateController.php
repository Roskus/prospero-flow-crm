<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TicketUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $user = new User;
        $ticket = Ticket::find($id);
        $customer = new Customer;
        $data['ticket'] = $ticket;
        $data['users'] = $user->getAllActiveByCompany((int) Auth::user()->company_id);
        $data['customers'] = $customer->getAllByCompanyId((int) Auth::user()->company_id);
        $attachments = collect(Storage::disk('public')->allFiles('attachments-tickets'.DIRECTORY_SEPARATOR.$ticket->id));
        $data['attachments'] = $attachments->map(function ($path) {
            return [
                'mimeType' => Storage::disk('public')->mimeType($path),
                'name' => File::basename($path),
                'url' => Storage::disk('public')->url($path),
            ];
        });

        return view('ticket.ticket', $data);
    }
}
