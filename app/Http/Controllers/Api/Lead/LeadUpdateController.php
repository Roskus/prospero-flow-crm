<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Http\Requests\LeadUpdateRequest;
use App\Models\Lead;
use App\Repositories\LeadRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class LeadUpdateController
{
    private LeadRepository $leadSaveRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadSaveRepository = $leadRepository;
    }

    #[OAT\Put(
        path: '/lead/{id}',
        summary: 'Update a Lead',
        security: [['bearerAuth' => []]],
        tags: ['Lead'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of Lead', schema: new OAT\Schema(type: 'integer')),
        ],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Lead')
        ),
        responses: [
            new OAT\Response(response: 200, description: 'Lead updated successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Lead not found'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function update(LeadUpdateRequest $request, int $id): JsonResponse
    {
        $lead = Lead::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }

        $data = $request->validated();
        $data['id'] = $id;
        $lead = $this->leadSaveRepository->save($data);

        return response()->json(['lead' => $lead->toArray()], 200);
    }
}
