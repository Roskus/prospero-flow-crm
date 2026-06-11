<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Contact;

use App\Repositories\ContactRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        tags: ['Contact'],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Contact')
        ),
        responses: [
            new OAT\Response(response: 400, description: 'Bad request: Please review required params'),
            new OAT\Response(response: 201, description: 'Contact created successfully'),
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $status = 400;
        $data = [];
        $valid = $request->validate([
            'contact_first_name' => ['required', 'max:50'],
            'lead_id' => ['required'],
            'contact_email' => ['required', 'max:254'],
            'contact_phone' => ['required', 'max:15'],
        ]);

        if ($valid) {
            $contact = $this->contactRepository->save($request->all());
            if ($contact) {
                $status = 201;
                $data['contact'] = ['id' => $contact->id];
            }
        }

        return response()->json($data, $status);
    }
}
