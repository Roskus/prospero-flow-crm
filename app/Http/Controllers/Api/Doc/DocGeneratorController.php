<?php

namespace App\Http\Controllers\Api\Doc;

use Illuminate\Http\JsonResponse;

$api_host = (getenv() === 'production') ? 'roskus.com' : 'localhost';
define('API_HOST', $api_host);

/**
 * @OA\Info(
 *    title="Hammer CRM API",
 *    version="1.0.0",
 *    description="",
 *    @OA\Contact(
 *        name="roskus",
 *        email="hello@roskus.com"
 *    )
 * ),
 * @OA\Server(
 *     url="/api",
 * ),
 * @OA\Server(url=API_HOST)
 */
class DocGeneratorController
{
    /**
     * @OA\Get(
     *     path="/resource.json",
     *     @OA\Response(response="200", description="API JSON Specification OpenAPI format")
     * )
     */
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
