<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Contact;

use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class ContactListController
{
    #[OAT\Get(
        path: '/contact',
        summary: 'Contact list by company',
        security: [['bearerAuth' => []]],
        tags: ['Contact'],
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
    public function index()
    {
        $count = Contact::where('company_id', Auth::user()->company_id)->count();
        $contacts = Contact::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'contacts' => $contacts]);
    }
}
