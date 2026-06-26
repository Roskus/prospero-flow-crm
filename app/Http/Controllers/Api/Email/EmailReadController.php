<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Email;

use App\Http\Requests\EmailReadRequest;
use App\Models\Email;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class EmailReadController
{
    #[OAT\Get(
        path: '/email/{id}',
        summary: 'Get Email information',
        security: [['bearerAuth' => []]],
        tags: ['Email'],
        parameters: [
            new OAT\Parameter(name: 'id', description: 'Id of the Email', in: 'path', required: true, schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Email found', content: new OAT\JsonContent(ref: '#/components/schemas/Email')),
            new OAT\Response(response: 404, description: 'Email not found'),
        ]
    )]
    public function read(EmailReadRequest $request, int $id): JsonResponse
    {
        $email = Email::where('company_id', Auth::user()->company_id)->find($id);

        if (! $email) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        return response()->json(['email' => $email]);
    }
}
