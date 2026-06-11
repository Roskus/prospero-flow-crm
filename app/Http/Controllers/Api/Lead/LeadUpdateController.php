<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Models\Lead;
use App\Repositories\LeadRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            new OAT\Response(response: 400, description: 'Bad request, please review the parameters'),
            new OAT\Response(response: 404, description: 'Lead not found'),
        ]
    )]
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
