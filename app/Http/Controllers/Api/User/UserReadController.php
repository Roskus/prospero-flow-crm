<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserReadController
{
    /**
     * @OA\Get (
     *     path="/user/{id}",
     *     summary="Get User information",
     *     tags={"User"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of User",
     *         required=true,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="User found",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *      ),
     *      @OA\Response(response="404", description="User not found")
     * )
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(int $id): \Illuminate\Http\JsonResponse
    {
        $user = null;
        try {
            $user = User::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
            $status = 200;
        } catch(ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['user' => $user], $status);
    }
}
