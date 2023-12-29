<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Models\Company;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyReadController
{
    /**
     * @OA\Get(
     *     path="/company/{id}",
     *     summary="Get Company information",
     *     tags={"Company"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of the Company",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Company found",
     *         @OA\JsonContent(ref="#/components/schemas/Company")
     *     ),
     *     @OA\Response(response="404", description="Company not found")
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(Request $request, int $id)
    {
        $company = null;
        $company_id = $id;
        if (Auth::user()->hasRole('SuperAdmin')) {
            $company_id = Auth::user()->company_id;
        }

        try {
            $company = Company::where('id', $company_id)->firstOrFail();
            $status = 200;
        } catch (ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json($company, $status);
    }
}
