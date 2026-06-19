<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Doc;

use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;
use OpenApi\Generator;

define('API_HOST', env('APP_API_URL'));
define('APP_VERSION', APP_VERSION);

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

        $openapi = Generator::scan([
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
