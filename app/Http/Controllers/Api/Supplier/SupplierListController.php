<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Supplier;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class SupplierListController
{
    #[OAT\Get(
        path: '/supplier',
        summary: 'Suppliers list by company',
        security: [['bearerAuth' => []]],
        tags: ['Supplier'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Suppliers list retrived successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/Supplier')
            ),
        ]
    )]
    public function index(Request $request)
    {
        $count = Supplier::where('company_id', Auth::user()->company_id)->count();
        $suppliers = Supplier::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'suppliers' => $suppliers]);
    }
}
