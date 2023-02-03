<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\MainController;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $campaign = Campaign::find($id);
        $data['campaign'] = $campaign;

        return view('campaign.campaign', $data);
    }
}
