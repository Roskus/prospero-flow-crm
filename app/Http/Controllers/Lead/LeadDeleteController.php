<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadDeleteController extends MainController
{
    /**
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request, int $id)
    {
        $lead = Lead::find($id);
        $lead->delete();

        return redirect('/lead')->with(['status' => true, 'message' => __('Lead deleted successfully')]);
    }
}
