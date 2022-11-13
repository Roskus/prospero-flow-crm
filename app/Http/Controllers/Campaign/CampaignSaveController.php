<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Models\Campaign;

class CampaignSaveController extends MainController
{
    public function save(Request $request)
    {
        if (empty($request->id)) {
            $campaign = new Campaign();
            $campaign->created_at = now();
        } else {
            $campaign = Campaign::find($request->id);
        }
        $campaign->subject = $request->subject;
        $campaign->updated_at = now();
        $campaign->save();
        return redirect('/campaign');
    }
}
