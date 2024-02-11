<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
 *         description="User modified successfully",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(response="400", description="Bad request"),
 *     @OA\Response(response="404", description="User not found")
 * )
 */
class UserUpdateController
{
    public function update(Request $request, int $id)
    {
        // Custom validation rules including optional password
        $rules = [
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:15',
            'photo' => 'nullable|string|max:255',
            'lang' => 'required|string|max:2',
            'timezone' => 'required|string|max:255',
            'password' => 'nullable|string|min:8', // Optional, only if user wants to update password
        ];
        // Validate the request data
        $validatedData = Validator::make($request->all(), $rules)->validate();

        // Find the user within the same company
        $user = User::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->first();

        if (! $user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // If password is provided, hash it before updating
        if (! empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            // Remove password from array if not provided to avoid updating it to null
            unset($validatedData['password']);
        }

        // Update the user with validated data
        $user->update($validatedData);

        return response()->json(['user' => $user], Response::HTTP_OK);
    }
}
