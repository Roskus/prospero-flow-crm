<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadCreateController
{
    /**
     * @OA\Post(
     *     path="/lead",
     *     summary="Create a Lead",
     *     tags={"Lead"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Product",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="201", description="Lead created successfully"),
     *     @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function create(Request $request): JsonResponse
    {
        $status = 400;
        $data = [];
        $valid = $request->validate([
            'first_name' => ['required', 'max:50'],
            'email' => ['required', 'max:254'],
            'phone' => ['required', 'max:15'],
            'country_id' => ['required', 'max:2'],
        ]);

        if ($valid) {
            $lead = new Lead();
            $lead->company_id = Auth::user()->company_id;
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
