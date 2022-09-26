<?php

namespace App\Http\Controllers\Api\Doc;

use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(title="Hammer CRM API", version="1.0")
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
        ]);

        return response()->json($openapi->toJson());
    }
}
