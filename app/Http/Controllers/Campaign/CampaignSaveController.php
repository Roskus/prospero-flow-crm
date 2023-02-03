<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\MainController;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignSaveController extends MainController
{
    public function save(Request $request)
    {
        if (empty($request->id)) {
            $campaign = new Campaign();
            $campaign->company_id = Auth::user()->company_id;
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
