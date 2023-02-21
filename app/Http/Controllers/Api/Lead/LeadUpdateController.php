<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Models\Lead;
use App\Repositories\LeadRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class LeadUpdateController
{
    private LeadRepository $leadSaveRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadSaveRepository = $leadRepository;
    }

    /**
     * @OA\Put(
     *     path="/lead/{id}",
     *     summary="Update a Lead",
     *     tags={"Lead"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Lead",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Lead")
     *     ),
     *     @OA\Response(response="200", description="Lead updated successfully"),
     *     @OA\Response(response="400", description="Bad request, please review the parameters"),
     *     @OA\Response(response="404", description="Lead not found")
     * )
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $status = 400;
        $data = [];

        $valid = $request->validate([
            'name' => ['max:50'],
            'business_name' => ['max:255'],
            'phone' => ['max:15'],
            'mobile' => ['max:15'],
            'email' => ['max:254'],
            'website' => ['max:255'],
            'linkedin' => ['max:255'],
            'tags' => ['max:255'],
        ]);

        if ($valid) {
            $lead = Lead::find($id);

            if ($lead) {
                $params['id'] = $id;
                $params = array_merge($params, $request->all());
                $lead = $this->leadSaveRepository->save($params);

                if ($lead) {
                    $status = 200;
                    $data['lead'] = $lead->toArray();
                }
            } else {
                $status = 404;
            }
        }

        return response()->json($data, $status);
    }
}
