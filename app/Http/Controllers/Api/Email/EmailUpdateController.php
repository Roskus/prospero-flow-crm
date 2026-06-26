<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Email;

use App\Http\Requests\EmailRequest;
use App\Services\EmailCreateService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class EmailUpdateController
{
    #[OAT\Put(
        path: '/email/{id}',
        summary: 'Update an Email',
        security: [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            description: 'Email data',
            content: new OAT\JsonContent(
                required: ['from', 'subject', 'body'],
                properties: [
                    new OAT\Property(property: 'from', type: 'string', format: 'email', example: 'sender@example.com'),
                    new OAT\Property(property: 'subject', type: 'string', maxLength: 255, example: 'Hello World'),
                    new OAT\Property(property: 'to', type: 'string', format: 'email', example: 'recipient@example.com'),
                    new OAT\Property(property: 'cc', type: 'string', format: 'email', example: 'cc@example.com'),
                    new OAT\Property(property: 'bcc', type: 'string', format: 'email', example: 'bcc@example.com'),
                    new OAT\Property(property: 'body', type: 'string', example: 'Email body content'),
                    new OAT\Property(property: 'signature', type: 'boolean', example: false),
                    new OAT\Property(property: 'attachment', type: 'array', items: new OAT\Items(type: 'string', format: 'binary')),
                ],
            ),
        ),
        tags: ['Email'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'ID of the Email', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Email updated successfully'),
            new OAT\Response(response: 404, description: 'Email not found'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function update(EmailRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $data['id'] = $id;

        $emailCreateService = new EmailCreateService;
        $email = $emailCreateService->create($data, $request->file('attachment'));

        return response()->json(['email' => $email]);
    }
}
