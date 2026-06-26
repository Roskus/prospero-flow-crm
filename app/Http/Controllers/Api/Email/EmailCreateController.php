<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Email;

use App\Http\Requests\EmailRequest;
use App\Services\EmailCreateService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class EmailCreateController
{
    #[OAT\Post(
        path: '/email',
        summary: 'Create an Email',
        security: [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Email data',
            required: true,
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
        responses: [
            new OAT\Response(response: 201, description: 'Email created successfully'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function create(EmailRequest $request): JsonResponse
    {
        $emailCreateService = new EmailCreateService;
        $email = $emailCreateService->create($request->validated(), $request->file('attachment'));

        return response()->json(['email' => $email], 201);
    }
}
