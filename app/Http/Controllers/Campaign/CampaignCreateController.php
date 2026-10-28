<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\MainController;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignCreateController extends MainController
{
    public function create(Request $request)
    {
        $campaign = new Campaign;
        $data['campaign'] = $campaign;
        $data['froms'] = Campaign::fromAddresses(Auth::user());

        return view('campaign.campaign', $data);
    }
}
