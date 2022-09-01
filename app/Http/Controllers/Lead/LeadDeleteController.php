<?php

namespace App\Http\Controllers\Lead;

use Illuminate\Http\Request;
use App\Models\Lead;

class LeadDeleteController
{
    public function delete(Request $request, int $id)
    {
        $lead = Lead::find($id);
        $lead->delete();
        return redirect('/lead');
    }
}
