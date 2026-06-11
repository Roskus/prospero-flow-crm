<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class UserListController
{
    #[OAT\Get(
        path: '/user',
        summary: 'Users list by company',
        security: [['bearerAuth' => []]],
        tags: ['User'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Users list retrieved successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/User')
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $count = User::where('company_id', Auth::user()->company_id)->count();
        $users = User::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'users' => $users]);
    }
}
