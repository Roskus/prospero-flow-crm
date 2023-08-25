<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Repositories\LeadRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class LeadCreateController
{
    private LeadRepository $leadSaveRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadSaveRepository = $leadRepository;
    }

    /**
     * @OA\Post(
     *     path="/lead",
     *     summary="Create a Lead",
     *     tags={"Lead"},
     *     security={{"bearerAuth": {} }},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  example="John Smith"
     *              ),
     *              @OA\Property(
     *                   property="business_name",
     *                   type="string",
     *                   example="John Smith LTD"
     *              ),
     *              @OA\Property(
     *                  property="phone",
     *                  type="int",
     *                  example="34123456789",
     *              ),
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *                  format="email",
     *                  example="john@smith.com",
     *              ),
     *              @OA\Property(
     *                  property="notes",
     *                  type="string",
     *                  example="Notes of the lead",
     *              ),
     *              @OA\Property(
     *                   property="country_id",
     *                   type="string",
     *                   example="ISO Country code 2 digits",
     *               )
     *          )
     *     ),
     *     @OA\Response(response="201", description="Lead created successfully"),
     *     @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function create(Request $request): JsonResponse
    {
        $status = 400;
        $data = [];
        $valid = $request->validate([
            'name' => ['required', 'max:80'],
            'email' => ['required', 'max:254'],
            'phone' => ['required', 'max:15'],
        ]);
        $data = $request->all();
        $data['country_id'] = ! empty($data['country_id']) ? $data['country_id'] : Auth::user()->company->country_id;

        $response = [];
        if ($valid) {
            $lead = $this->leadSaveRepository->save($data);
            if (! empty($lead)) {
                $status = 201;
                $response = $lead;
            }
        } else {
            $response = $valid->errors();
        }

        return response()->json($response, $status);
    }
}
