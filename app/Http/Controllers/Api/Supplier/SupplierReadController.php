<?php

namespace App\Http\Controllers\Api\Supplier;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierReadController
{
    /**
     * @OA\Get(
     *     path="/supplier/{id}",
     *     summary="Get Supplier information",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Supplier",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Supplier found",
     *         @OA\JsonContent(ref="#/components/schemas/Supplier")
     *     ),
     *     @OA\Response(response="404", description="Supplier not found")
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(Request $request, int $id)
    {
        $supplier = null;
        try {
            $supplier = Supplier::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
            $status = 200;
        } catch (ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['supplier' => $supplier], $status);
    }
}
