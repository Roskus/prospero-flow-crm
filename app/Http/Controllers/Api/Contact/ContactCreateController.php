<?php

namespace App\Http\Controllers\Api\Contact;

use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactCreateController
{
    /**
     * @OA\Post(
     *     path="/contact",
     *     summary="Create a contact",
     *     tags={"Contact"},
     *     @OA\Response(response="400", description="Bad request: Please review required params"),
     *     @OA\Response(response="201", description="Contact created successfully")
     * )
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $status = 400;
        $data = [];
        $valid = $request->validate([
            'lead_id' => ['required'],
            'email' => ['required', 'max:254'],
            'first_name' => ['required', 'max:50'],
            'phone' => ['required', 'max:15'],
        ]);

        if ($valid) {
            $contact = new Contact();
            //$contact->company_id = $request->company_id; // @TODO Get this from Token
            $contact->lead_id = $request->lead_id;
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
