<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserListController
{
    /**
     * @OA\Get(
     *     path="/user",
     *     summary="Users list by company",
     *     tags={"User"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="Users list retrived successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $count = User::where('company_id', Auth::user()->company_id)->count();
        $users = User::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'users' => $users]);
    }
}
