<?php

declare(strict_types=1);

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
     *      security={ {"bearer": {} }},
     *      @OA\Response(
     *          response="200",
     *          description="Lead list retrived successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Lead")
     *      )
     * )
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(['leads' => Lead::all()]);
    }
}
