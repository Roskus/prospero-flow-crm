<?php

namespace App\Http\Controllers\Api\Supplier;

use Illuminate\Http\Request;

class SupplierUpdateController
{
    /**
     * @OA\Put(
     *     path="/supplier/{id}",
     *     summary="Update a Supplier",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Supplier",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Supplier updated successfully"),
     *     @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function update(Request $request, int $id) {}
}
