<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class CompanyListController
{
    #[OAT\Get(
        path: '/company',
        summary: 'Company list',
        security: [['bearerAuth' => []]],
        tags: ['Company'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Company list retrieved successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/Company')
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $count = 0;
        if (Auth::user()->hasRole('SuperAdmin')) {
            $count = Company::count();
            $companies = Company::with('country')->get();
        } else {
            $count = Company::where('id', Auth::user()->company_id)->count();
            $companies = Company::with('country')
                ->where('id', Auth::user()->company_id)
                ->get();
        }

        return response()->json(['count' => $count, 'companies' => $companies]);
    }
}
