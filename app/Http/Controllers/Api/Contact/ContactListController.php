<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Contact;

use App\Http\Requests\ContactListRequest;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class ContactListController
{
    #[OAT\Get(
        path: '/contact',
        summary: 'Contact list by company',
        security: [['bearerAuth' => []]],
        tags: ['Contact'],
        parameters: [
            new OAT\Parameter(name: 'linkedin', description: 'LinkedIn profile URL; lists only the contacts of that profile, however the URL is written', in: 'query', required: false, schema: new OAT\Schema(type: 'string', maxLength: 255, example: 'https://www.linkedin.com/in/jane-doe')),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Contact list retrieved successfully',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'count', description: 'Total number of results', type: 'integer', example: 1),
                        new OAT\Property(property: 'contacts', type: 'array', items: new OAT\Items(ref: '#/components/schemas/Contact')),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function index(ContactListRequest $request): JsonResponse
    {
        $profile = $request->linkedinProfile();
        $contacts = Contact::where('company_id', Auth::user()->company_id)
            ->when($profile !== null, fn ($query) => $query->linkedinProfile($profile))
            ->get();

        return response()->json(['count' => $contacts->count(), 'contacts' => $contacts]);
    }
}
