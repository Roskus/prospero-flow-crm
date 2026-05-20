<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Repositories\CompanyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyUpdateController
{
    public function __construct(private CompanyRepository $companyRepository) {}

    /**
     * @OA\Put(
     *     path="/company/{id}",
     *     summary="Update a Company",
     *     tags={"Company"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Company",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Company")
     *     ),
     *     @OA\Response(response="200", description="Company updated successfully"),
     *     @OA\Response(response="400", description="Bad request, please review the parameters"),
     *     @OA\Response(response="404", description="Company not found")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        if (Auth::user()->company_id !== $id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $params = array_merge(['id' => $id], $request->all());
        $company = $this->companyRepository->save($params);

        return response()->json($company, $company ? 200 : 400);
    }
}
