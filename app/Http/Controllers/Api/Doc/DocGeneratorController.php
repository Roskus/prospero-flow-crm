<?php

namespace App\Http\Controllers\Api\Doc;

use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(title="Hammer CRM API", version="1.0")
 */
class DocGeneratorController
{
    public function render() : JsonResponse
    {
        $app_path = app_path();
        $openapi = \OpenApi\Generator::scan([
            $app_path.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Api',
            $app_path.DIRECTORY_SEPARATOR.'Models',
        ]);
        return response()->json($openapi->toJson());
    }
}
