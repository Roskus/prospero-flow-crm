<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Models\Campaign;

class CampaignCreateController extends MainController
{
    public function create(Request $request)
    {
        $campaign = new Campaign();
        $data['campaign'] = $campaign;
        return view('campaign.campaign', $data);
    }
}
