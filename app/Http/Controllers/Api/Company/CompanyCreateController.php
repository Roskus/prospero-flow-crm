<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Http\Requests\CompanyCreateRequest;
use App\Repositories\CompanyRepository;
use Illuminate\Http\JsonResponse;

class CompanyCreateController
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function create(CompanyCreateRequest $request): JsonResponse
    {
        $company = $this->companyRepository->save($request->validated());

        return response()->json(['company' => $company], 201);
    }
}
