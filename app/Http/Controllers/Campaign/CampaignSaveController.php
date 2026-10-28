<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\MainController;
use App\Http\Requests\CampaignSaveRequest;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CampaignSaveController extends MainController
{
    public function save(CampaignSaveRequest $request): RedirectResponse
    {
        if (empty($request->id)) {
            $campaign = new Campaign;
            $campaign->company_id = Auth::user()->company_id;
        } else {
            $campaign = Campaign::where('company_id', Auth::user()->company_id)->findOrFail($request->id);
        }
        $campaign->name = $request->name;
        $campaign->subject = $request->subject;
        $campaign->from = $request->from;
        $campaign->body = $request->body;
        $campaign->tags = ! empty($request->tags) ? array_values(array_filter(array_map('trim', explode(',', $request->tags)))) : null;
        $campaign->recipient_type = $request->recipient_type;
        $campaign->audience = $request->audience;
        $campaign->schedule_send_date = $request->schedule_send_date;
        $campaign->schedule_send_time = $request->schedule_send_time;
        $campaign->schedule_at = Campaign::scheduleMoment($request->schedule_send_date, $request->schedule_send_time, Auth::user()->timezone);
        $campaign->save();

        return redirect('/campaign')->with(['status' => 'success', 'message' => __('Campaign saved successfully')]);
    }
}
