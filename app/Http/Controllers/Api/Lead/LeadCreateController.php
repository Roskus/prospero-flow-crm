<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Repositories\LeadRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            $lead = $this->leadSaveRepository->save($request->all());
            if (! empty($lead)) {
                $status = 201;
                $data['lead'] = ['id' => $lead->id];
            }
        }

        return response()->json($data, $status);
    }
}
