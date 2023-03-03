<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Repositories\CompanyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyCreateController
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function create(Request $request): JsonResponse
    {
        $status = 400;
        $company = $this->companyRepository->save($request->all());
        if ($company) {
            $status = 201;
        }

        return response()->json(['company' => $company, $status]);
    }
}
