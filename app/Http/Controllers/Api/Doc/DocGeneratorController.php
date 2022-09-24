<?php

namespace App\Http\Controllers\Api\Doc;

/**
 * @OA\Info(title="Hammer CRM API", version="1.0")
 */
class DocGeneratorController
{
    /**
     * @OA\Get(
     *     path="/resource.json",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    public function render()
    {
        $app_path = base_path();
        $openapi = OpenApi\Generator::scan([
            $app_path.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Api',
        ]);

        return response()->json($openapi->toJson());
    }
}
