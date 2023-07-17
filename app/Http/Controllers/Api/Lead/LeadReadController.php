<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Models\Lead;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LeadReadController
{
    /**
     * @OA\Get(
     *     path="/lead/{id}",
     *     summary="Get Lead information",
     *     tags={"Lead"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Lead",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Lead found",
     *         @OA\JsonContent(ref="#/components/schemas/Lead")
     *     ),
     *     @OA\Response(response="404", description="Lead not found")
     * )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $lead = null;
        try {
            $lead = Lead::where('company_id', Auth::user()->company_id)->where('id', $id)->firstOrFail();
            $status = 200;
        } catch (ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['lead' => $lead], $status);
    }
}
