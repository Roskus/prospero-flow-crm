<?php

namespace App\Http\Controllers\Api\Product;

use Illuminate\Http\Request;

class ProductUpdateController
{
    /**
     * @OA\Put (
     *      path="/product/{id}",
     *      summary="Update a Product",
     *      tags={"Product"},
     *      security={{"bearerAuth": {} }},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Product",
     *         required=true,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Product updated successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function update(Request $request, int $id)
    {
    }
}
