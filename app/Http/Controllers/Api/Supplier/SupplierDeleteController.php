<?php

namespace App\Http\Controllers\Api\Supplier;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierDeleteController
{
    /**
     * @OA\Delete (
     *      path="/supplier/{id}",
     *      summary="Delete a Supplier",
     *      tags={"Supplier"},
     *      security={{"bearerAuth": {} }},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Supplier",
     *         required=true,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Supplier deleted successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * @return JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        $supplier = Supplier::find($id)->where('company_id', Auth::user()->company_id)->get();
        $supplier->delete();

        return response()->json(['message' => 'Supplier deleted successfully']);
    }
}
