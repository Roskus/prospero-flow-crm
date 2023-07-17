<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\MainController;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignDeleteController extends MainController
{
    public function delete(Request $request, int $id)
    {
        $campaign = Campaign::find($id);
        if ($campaign->company_id !== Auth::user()->company_id && Auth::user()->company_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $campaign->delete();

        return redirect('campaign');
    }
}
