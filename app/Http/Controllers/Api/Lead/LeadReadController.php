<?php

namespace App\Http\Controllers\Api\Lead;

use App\Models\Lead;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class LeadReadController
{
    /**
     * @OA\Get (
     *     path="/lead/{id}",
     *     summary="Get Lead information",
     *     tags={"Leads"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Lead",
     *         required=true,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Lead found"),
     *      @OA\Response(response="404", description="Lead not found")
     * )
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $lead = null;
        try {
            $lead = Lead::findOrFail($id);
            $status = 200;
        } catch(ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['lead' => $lead], $status);
    }
}
