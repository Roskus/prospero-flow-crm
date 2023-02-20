<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Doc;

use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

define('API_HOST', env('APP_API_URL'));

#[
    OAT\Info(
        version: '1.0.3',
        description: '',
        title: 'Prospect Flow CRM API',
        contact:new OAT\Contact(
            name: 'roskus',
            email: 'hello@roskus.com'
        )
    )
]
/**
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Authorisation with JWT generated tokens",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * ),
 * @OA\Server(
 *     url="/api",
 * ),
 * @OA\Server(url=API_HOST)
 */
class DocGeneratorController
{
    public function render(): JsonResponse
    {
        $app_path = app_path();
        $exclude = [];
        $pattern = '*.php';

        $openapi = \OpenApi\Generator::scan([
            $app_path.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Api',
            $app_path.DIRECTORY_SEPARATOR.'Models',
        ],
            [
                'exclude' => $exclude,
                'pattern' => $pattern,
            ]);

        return response()->json($openapi->toJson());
    }
}
