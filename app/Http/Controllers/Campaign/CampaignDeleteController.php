<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\MainController;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignDeleteController extends MainController
{
    public function delete(Request $request, int $id)
    {
        $campaign = Campaign::find($id);
        $campaign->delete();

        return redirect('campaign');
    }
}
