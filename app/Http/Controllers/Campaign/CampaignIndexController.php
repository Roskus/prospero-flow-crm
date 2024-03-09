<?php

declare(strict_types=1);

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
        $data['bootstrap_colors'] = [
            'text-bg-primary',
            'text-bg-secondary',
            'text-bg-success',
            'text-bg-danger',
            'text-bg-warning',
            'text-bg-info',
        ];
        $data['campaigns'] = $campaign->getAllByCompany((int) Auth::user()->company_id);

        return view('campaign.index', $data);
    }
}
