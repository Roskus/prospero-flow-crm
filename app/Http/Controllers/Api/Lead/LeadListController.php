<?php

namespace App\Http\Controllers\Api\Lead;

use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class LeadListController
{
    /**
     * @OA\Get(
     *      path="/leads",
     *      summary="Lead list by company",
     *      tags={"Leads"},
     *      @OA\Response(response="200", description="Lead list retrived successfully")
     * )
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request) : JsonResponse
    {
        return response()->json(['leads' => Lead::all()]);
    }
}
