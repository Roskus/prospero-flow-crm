<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Http\Requests\CompanyDeleteRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

/**
 * Controller for company deletion.
 *
 * @group Companies
 */
class CompanyDeleteController
{
    /**
     * @OA\Delete (
     *      path="/company/{id}",
     *      summary="Delete a Company",
     *      tags={"Company"},
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Company",
     *         required=true,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Company deleted successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * Delete a company by ID.
     *
     * @authenticated
     *
     * @return JsonResponse
     */
    public function delete(CompanyDeleteRequest $request, int $id) // @SuppressWarnings(S1172) - $request used for validation
    {
        $company = Company::find($id);
        if (! $company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }
}
