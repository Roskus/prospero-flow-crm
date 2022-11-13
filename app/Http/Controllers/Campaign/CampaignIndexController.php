<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\MainController;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignIndexController extends MainController
{
    public function index(Request $request)
    {
        $campaign = new Campaign();
        $data['campaigns'] = $campaign->getAllByCompany(Auth::user()->company_id);
        return view('campaign.index', $data);
    }
}
