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
                description: 'Contact list retrived successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/Contact')
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
