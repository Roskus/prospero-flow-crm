<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\MainController;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignCreateController extends MainController
{
    public function create(Request $request)
    {
        $campaign = new Campaign();
        $data['campaign'] = $campaign;

        return view('campaign.campaign', $data);
    }
}
