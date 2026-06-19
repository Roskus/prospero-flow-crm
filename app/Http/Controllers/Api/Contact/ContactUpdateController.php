<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Contact;

use App\Http\Requests\ContactUpdateRequest;
use App\Repositories\ContactRepository;
use OpenApi\Attributes as OAT;

class ContactUpdateController
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    #[OAT\Put(
        path: '/contact/{id}',
        summary: 'Update a Contact',
        security: [['bearerAuth' => []]],
        tags: ['Contact'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                in: 'path',
                description: 'Id of Contact',
                required: true,
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Contact')
        ),
        responses: [
            new OAT\Response(response: 200, description: 'Contact updated successfully'),
            new OAT\Response(response: 400, description: 'Bad request, please review the parameters'),
            new OAT\Response(response: 404, description: 'Contact not found'),
        ]
    )]
    public function update(ContactUpdateRequest $request, int $id)
    {
        $status = 400;
        $params['id'] = $id;
        $params = array_merge($params, $request->all());
        $contact = $this->contactRepository->save($params);
        if ($contact) {
            $status = 200;
        }

        return response()->json(['contact' => $contact], $status);
    }
}
