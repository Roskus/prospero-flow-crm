<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\MainController;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CampaignCloneController extends MainController
{
    public function clone(int $id): RedirectResponse
    {
        $campaign = Campaign::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $copy = $campaign->duplicateAsDraft();

        return redirect("/campaign/update/$copy->id")->with(['status' => 'success', 'message' => __('Campaign duplicated as a draft, set a schedule date to send it')]);
    }
}
