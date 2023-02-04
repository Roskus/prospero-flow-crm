<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Contact;

use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class ContactCreateController
{
    #[OAT\Post(
        path: '/contact',
        summary: 'Create a contact',
        security: [['bearerAuth']],
        tags: ['Contact'],
        responses: [
            new OAT\Response(response: 201, description: 'Contact created successfully'),
            new OAT\Response(response: 400, description: 'Bad request: Please review required params'),
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $status = 400;
        $data = [];
        $valid = $request->validate([
            'first_name' => ['required', 'max:50'],
            'lead_id' => ['required'],
            'email' => ['required', 'max:254'],
            'phone' => ['required', 'max:15'],
        ]);

        if ($valid) {
            $contact = new Contact();
            $contact->company_id = Auth::user()->company_id;
            $contact->lead_id = $request->lead_id;
            $contact->customer_id = $request->customer_id;
            $contact->first_name = $request->first_name;
            $contact->last_name = $request->last_name;
            $contact->email = $request->email;
            $contact->phone = $request->phone;
            $contact->linkedin = $request->linkedin;
            $contact->created_at = now();
            $contact->save();
            $status = 201;
            $data['contact'] = ['id' => $contact->id];
        }

        return response()->json($data, $status);
    }
}
