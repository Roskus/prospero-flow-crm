<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Doc;

use Illuminate\Http\JsonResponse;

define('API_HOST', env('APP_API_URL'));

/**
 * @OA\Info(
 *     title="Hammer CRM API",
 *     version="1.0.2",
 *     description="",
 *     @OA\Contact(
 *         name="roskus",
 *         email="hello@roskus.com"
 *     ),
 * ),
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
        $openapi = \OpenApi\Generator::scan([
            $app_path.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Api',
            $app_path.DIRECTORY_SEPARATOR.'Models',
        ]);

        return response()->json($openapi->toJson());
    }
}
