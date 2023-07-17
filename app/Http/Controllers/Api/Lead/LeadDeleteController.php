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
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        $lead = Lead::find($id)->where('user_id', Auth::id())->get();
        $lead->delete();

        return response()->json(['message' => 'Lead deleted successfully']);
    }
}
