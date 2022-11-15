<?php

namespace App\Http\Controllers\Api\Lead;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadReadController
{
    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $lead = Lead::find($id);
        return response()->json(['lead' => $lead]);
    }
}
