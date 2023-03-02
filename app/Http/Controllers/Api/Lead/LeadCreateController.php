<?php

namespace App\Http\Controllers\Api\Lead;

use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadCreateController
{
    /**
     * @OA\Post (
     *      path="/lead",
     *      summary="Create a Lead",
     *      tags={"Leads"},
     *      @OA\Response(response="201", description="Lead created successfully")
     * )
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function create(Request $request): JsonResponse
    {
        $status = 400;
        $data = [];
        $valid = $request->validate([
            'company_id' => ['required'],
            'email' => ['required', 'max:254'],
            'first_name' => ['required', 'max:50'],
            'phone' => ['required', 'max:15'],
            'country_id' => ['required', 'max:2'],
        ]);

        if ($valid) {
            $lead = new Lead();
            $lead->company_id = $request->company_id;
            $lead->name = $request->name;
            $lead->business_name = $request->business_name;
            $lead->phone = $request->phone;
            $lead->mobile = $request->mobile;
            $lead->email = $request->email;
            $lead->website = $request->website;
            $lead->linkedin = $request->linkedin;
            $lead->tags = \trim($request->tags);
            $lead->created_at = now();
            $lead->save();
            $status = 201;
            $data['lead'] = ['id' => $lead->id];
        }

        return response()->json($data, $status);
    }
}
