<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Contact;

use App\Repositories\ContactRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ContactUpdateController
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    /**
     * @OA\Put(
     *     path="/contact/{id}",
     *     summary="Update a Contact",
     *     tags={"Contact"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Contact",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Contact")
     *     ),
     *     @OA\Response(response="200", description="Contact updated successfully"),
     *     @OA\Response(response="400", description="Bad request, please review the parameters"),
     *     @OA\Response(response="404", description="Contact not found")
     * )
     *
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
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
