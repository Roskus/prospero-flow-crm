<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Supplier;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class SupplierReadController
{
    #[OAT\Get(
        path: '/supplier/{id}',
        summary: 'Get Supplier information',
        security: [['bearerAuth' => []]],
        tags: ['Supplier'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                in: 'path',
                description: 'Id of Supplier',
                required: true,
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Supplier found',
                content: new OAT\JsonContent(ref: '#/components/schemas/Supplier')
            ),
            new OAT\Response(response: 404, description: 'Supplier not found'),
        ]
    )]
    public function read(Request $request, int $id)
    {
        $supplier = null;
        try {
            $supplier = Supplier::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
            $status = 200;
        } catch (ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['supplier' => $supplier], $status);
    }
}
