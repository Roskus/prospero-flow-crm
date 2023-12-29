<?php

namespace App\Http\Controllers\Api\Supplier;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierListController
{
    /**
     * @OA\Get(
     *      path="/supplier",
     *      summary="Suppliers list by company",
     *      tags={"Supplier"},
     *      security={{"bearerAuth": {} }},
     *      @OA\Response(
     *          response="200",
     *          description="Suppliers list retrived successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Supplier")
     *      )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $count = Supplier::where('company_id', Auth::user()->company_id)->count();
        $suppliers = Supplier::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'suppliers' => $suppliers]);
    }
}
