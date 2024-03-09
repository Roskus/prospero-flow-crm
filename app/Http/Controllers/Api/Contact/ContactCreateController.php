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
     *     @OA\RequestBody(
     *          required=true,
     *          description="Contact data",
     *          @OA\JsonContent(
     *              required={"contact_first_name", "lead_id", "contact_email", "contact_phone"},
     *              @OA\Property(property="contact_first_name", type="string", maxLength=50, example="John"),
     *              @OA\Property(property="lead_id", type="integer", example=123),
     *              @OA\Property(property="contact_email", type="string", maxLength=254, format="email", example="john@example.com"),
     *              @OA\Property(property="contact_phone", type="string", maxLength=15, example="123-456-7890")
     *          ),
     *     ),
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
                $data['contact'] = ['id' => $contact->id];
            }
        }

        return response()->json($data, $status);
    }
}
