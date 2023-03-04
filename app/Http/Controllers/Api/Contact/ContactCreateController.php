<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Contact;

use App\Models\Contact;
use App\Repositories\ContactRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ContactCreateController
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    /**
     * @OA\Post(
     *     path="/contact",
     *     summary="Create a contact",
     *     tags={"Contact"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(response="400", description="Bad request: Please review required params"),
     *     @OA\Response(response="201", description="Contact created successfully")
     * )
     */
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
                $data['contact'] = $contact;
            }
        }

        return response()->json($data, $status);
    }
}
