<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Http\Requests\LeadRequest;
use App\Repositories\LeadRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class LeadCreateController
{
    private LeadRepository $leadSaveRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadSaveRepository = $leadRepository;
    }

    #[OAT\Post(
        path: '/lead',
        summary: 'Create a Lead',
        security: [['bearerAuth' => []]],
        tags: ['Lead'],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Lead')
        ),
        responses: [
            new OAT\Response(response: 201, description: 'Lead created successfully'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function create(LeadRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['country_id'] = ! empty($data['country_id']) ? $data['country_id'] : Auth::user()->company->country_id;

        $lead = $this->leadSaveRepository->save($data);

        return response()->json($lead, 201);
    }
}
