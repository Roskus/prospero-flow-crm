<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Email;

use App\Http\Requests\EmailListRequest;
use App\Models\Email;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class EmailListController
{
    #[OAT\Get(
        path: '/email',
        summary: 'Email list by company',
        security: [['bearerAuth' => []]],
        tags: ['Email'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Email list retrieved successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/Email')
            ),
        ]
    )]
    public function index(EmailListRequest $request): JsonResponse
    {
        $emails = Email::where('company_id', Auth::user()->company_id)
            ->orderByDesc('id')
            ->get();

        return response()->json(['count' => $emails->count(), 'emails' => $emails]);
    }
}
