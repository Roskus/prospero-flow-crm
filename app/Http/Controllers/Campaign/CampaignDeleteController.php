<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\MainController;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CampaignDeleteController extends MainController
{
    public function delete(int $id): RedirectResponse
    {
        $campaign = Campaign::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();
        $campaign->delete();

        return redirect('campaign')->with(['status' => 'success', 'message' => __('Campaign deleted successfully')]);
    }
}
