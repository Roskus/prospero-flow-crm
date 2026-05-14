<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Lead;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class LeadDeleteController extends MainController
{
    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function delete(Request $request, int $id)
    {
        $lead = Lead::find($id);
        $status = $lead->delete();

        return redirect('/lead')->with(['status' => $status ? 'success' : 'error', 'message' => __('Lead deleted successfully')]);
    }
}
