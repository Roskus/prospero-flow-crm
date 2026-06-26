<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Contact;

use App\Http\Requests\ContactRequest;
use App\Repositories\ContactRepository;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class ContactCreateController
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    #[OAT\Post(
        path: '/contact',
        summary: 'Create a contact',
        security: [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Contact')
        ),
        tags: ['Contact'],
        responses: [
            new OAT\Response(response: 201, description: 'Contact created successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
        ]
    )]
    public function create(ContactRequest $request): JsonResponse
    {
        $contact = $this->contactRepository->save($request->validated());

        return response()->json(['contact' => ['id' => $contact->id]], 201);
    }
}
