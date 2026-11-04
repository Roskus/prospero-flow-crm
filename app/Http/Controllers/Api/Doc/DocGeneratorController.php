<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Doc;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;
use OpenApi\Generator;

define('API_HOST', env('APP_API_URL'));

#[
    OAT\Info(
        version: APP_VERSION,
        description: '',
        title: 'Prospero Flow CRM API',
        contact: new OAT\Contact(
            name: 'roskus',
            email: 'hello@roskus.com'
        )
    )
]
#[OAT\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    description: 'Authorisation with JWT generated tokens',
    name: 'Authorization',
    in: 'header',
    bearerFormat: 'JWT',
    scheme: 'bearer'
)]
#[OAT\Schema(
    schema: 'Pagination',
    description: 'Pagination fields added to a paginated list',
    properties: [
        new OAT\Property(property: 'current_page', type: 'integer', example: 1),
        new OAT\Property(property: 'first_page_url', type: 'string', format: 'uri', example: 'https://crm.example.com/api/customer?page=1'),
        new OAT\Property(property: 'from', type: 'integer', example: 1, nullable: true),
        new OAT\Property(property: 'last_page', type: 'integer', example: 4),
        new OAT\Property(property: 'last_page_url', type: 'string', format: 'uri', example: 'https://crm.example.com/api/customer?page=4'),
        new OAT\Property(
            property: 'links',
            type: 'array',
            items: new OAT\Items(
                properties: [
                    new OAT\Property(property: 'url', type: 'string', format: 'uri', nullable: true),
                    new OAT\Property(property: 'label', type: 'string', example: '1'),
                    new OAT\Property(property: 'active', type: 'boolean', example: true),
                ],
                type: 'object'
            )
        ),
        new OAT\Property(property: 'next_page_url', type: 'string', format: 'uri', example: 'https://crm.example.com/api/customer?page=2', nullable: true),
        new OAT\Property(property: 'path', type: 'string', format: 'uri', example: 'https://crm.example.com/api/customer'),
        new OAT\Property(property: 'per_page', type: 'integer', example: 15),
        new OAT\Property(property: 'prev_page_url', type: 'string', format: 'uri', example: null, nullable: true),
        new OAT\Property(property: 'to', type: 'integer', example: 15, nullable: true),
        new OAT\Property(property: 'total', type: 'integer', example: 52),
    ],
    type: 'object'
)]
#[OAT\Server(url: '/api')]
#[OAT\Server(url: API_HOST)]
class DocGeneratorController
{
    public function render(): JsonResponse
    {
        $openapi = (new Generator)->generate([
            app_path('Http/Controllers/Api'),
            app_path('Models'),
            app_path('ValueObjects'),
        ]);

        return response()->json($openapi->toJson());
    }
}
