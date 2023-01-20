<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Http\Controllers\Api\ApiController;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadListController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/lead",
     *      summary="Lead list by company",
     *      tags={"Lead"},
     *      security={{"bearerAuth": {} }},
     *      @OA\Response(
     *          response="200",
     *          description="Lead list retrived successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Lead")
     *      )
     * )
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request): JsonResponse
    {
        $count = Lead::where('company_id', Auth::user()->company_id)->count();
        $leads = Lead::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'leads' => $leads]);
    }
}
