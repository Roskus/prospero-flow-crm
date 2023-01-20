<?php

namespace App\Http\Controllers\Api\Contact;

use App\Models\Contact;
use Illuminate\Support\Facades\Auth;

class ContactListController
{
    /**
     * @OA\Get(
     *      path="/contact",
     *      summary="Contact list by company",
     *      tags={"Contact"},
     *      security={{"bearerAuth": {} }},
     *      @OA\Response(
     *          response="200",
     *          description="Contact list retrived successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Contact")
     *      )
     * )
     */
    public function index()
    {
        $count = Contact::where('company_id', Auth::user()->company_id)->count();
        $contacts = Contact::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'contacts' => $contacts]);
    }
}
