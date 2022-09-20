<?php

namespace App\Http\Controllers\Lead;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadDeleteController
{
    public function delete(Request $request, int $id)
    {
        $lead = Lead::find($id);
        $lead->delete();

        return redirect('/lead');
    }
}
