<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class UserCreateController
{
    #[OAT\Post(
        path: '/user',
        summary: 'Create an user',
        security: [],
        tags: ['User'],
        responses: [
            new OAT\Response(response: 200, description: 'User deleted successfully'),
            new OAT\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = null;
        try {
            $user = new User();
            $user->fill([
                'company_id' => Auth::user()->company_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);
            $user->save();
            $status = 201;
        } catch(\Throwable $throwable) {
            $status = 400;
        }

        return response()->json(['user' => $user], $status);
    }
}
