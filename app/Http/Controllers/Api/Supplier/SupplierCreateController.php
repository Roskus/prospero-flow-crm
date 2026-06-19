<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Supplier;

use App\Http\Requests\API\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class SupplierCreateController
{
    #[OAT\Post(
        path: '/supplier',
        summary: 'Create a Supplier',
        security: [['bearerAuth' => []]],
        tags: ['Supplier'],
        requestBody: new OAT\RequestBody(
            required: true,
            description: 'Supplier attributes',
            content: new OAT\JsonContent(
                required: ['name', 'country'],
                properties: [
                    new OAT\Property(property: 'name', type: 'string', example: 'Sony'),
                    new OAT\Property(property: 'email', type: 'string', format: 'email', example: 'supplier@sony.com'),
                    new OAT\Property(property: 'phone', type: 'string', example: '+1234567890'),
                    new OAT\Property(property: 'country', type: 'string', example: 'UK'),
                    new OAT\Property(property: 'business_name', type: 'string', example: 'Sony Corporation'),
                    new OAT\Property(property: 'vat', type: 'string', example: 'GB123456789'),
                    new OAT\Property(property: 'website', type: 'string', example: 'https://www.sony.com'),
                    new OAT\Property(property: 'province', type: 'string', example: 'London'),
                    new OAT\Property(property: 'city', type: 'string', example: 'London'),
                    new OAT\Property(property: 'street', type: 'string', example: '123 Main St'),
                    new OAT\Property(property: 'zipcode', type: 'string', example: 'W1A 1AA'),
                ]
            )
        ),
        responses: [
            new OAT\Response(response: 201, description: 'Supplier created successfully'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function create(SupplierRequest $request): JsonResponse
    {
        $data = [];
        try {
            $supplier = new Supplier;
            $supplier->company_id = Auth::user()->company_id;
            $supplier->name = $request->name;
            $supplier->business_name = $request->business_name;
            $supplier->vat = $request->vat;
            $supplier->phone = $request->phone;
            $supplier->email = $request->email;
            $supplier->website = $request->website;
            $supplier->country_id = $request->country;
            $supplier->province = $request->province;
            $supplier->city = $request->city;
            $supplier->street = $request->street;
            $supplier->zipcode = $request->zipcode;
            $supplier->created_at = now();
            $supplier->save();
            $status = 201;
            $data['supplier'] = ['id' => $supplier->id];

            return response()->json($data, $status);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: '.$e->getMessage(),
            ], 500);
        }
    }
}
