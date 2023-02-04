<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserUpdateController
{
    /**
     * @OA\Put(
     *     path="/user/{id}",
     *     summary="Update User information",
     *     tags={"User"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of User",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User found",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response="404", description="User not found")
     * )
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $user = null;
        try {
            $user = User::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
            $user->fill([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);
            $user->save();
            $status = 200;
        } catch(ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['user' => $user], $status);
    }
}
