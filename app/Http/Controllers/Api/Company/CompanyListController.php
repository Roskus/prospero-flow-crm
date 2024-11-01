<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class CompanyListController
{
    /**
     * @OA\Get(
     *     path="/company",
     *     summary="Company list by company",
     *     tags={"Company"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="Company list retrived successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Company")
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $count = 0;
        if (Auth::user()->hasRole('SuperAdmin')) {
            $count = Company::count();
            $companies = Company::with('country')->get();
        } else {
            $count = Company::where('company_id', Auth::user()->company_id)->count();
            $companies = Company::with('country')  // Cargar country para evitar problemas de relación
                ->where('company_id', Auth::user()->company_id)
                ->get();
        }

        return response()->json(['count' => $count, 'companies' => $companies]);
    }
}
