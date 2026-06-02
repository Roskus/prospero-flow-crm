<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for lead deletion.
 *
 * @group Leads
 */
class LeadDeleteController
{
    /**
     * @OA\Delete (
     *      path="/lead/{id}",
     *      summary="Delete a Lead",
     *      tags={"Lead"},
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Lead",
     *         required=true,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Lead deleted successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * Delete a lead by ID.
     *
     * @authenticated
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        $lead = Lead::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }

        $lead->delete();

        return response()->json(['message' => 'Lead deleted successfully']);
    }
}
