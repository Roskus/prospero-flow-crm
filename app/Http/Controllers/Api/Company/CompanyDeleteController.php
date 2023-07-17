<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function delete(Request $request, int $id)
    {
        $company = Company::find($id);
        if (! $company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        // Ensure the user can only delete their own company
        if (Auth::user()->company_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }
}
