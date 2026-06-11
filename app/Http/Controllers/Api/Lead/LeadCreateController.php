<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Repositories\LeadRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            new OAT\Response(response: 400, description: 'Bad request, please review the parameters'),
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $status = 400;
        $data = [];
        $valid = $request->validate([
            'name' => ['required', 'max:120'],
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
