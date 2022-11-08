<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Route;

/**
 * PWA
 */
class ManifestController extends Controller
{
    /**
     * https://developer.mozilla.org/es/docs/Web/Manifest
     * Render
     * @return json|\Illuminate\Http\JsonResponse
     */
    public function renderWebManifest(Request $request, string $locale = 'en')
    {
        $manifest = [
            "lang" => $locale,
            "name" => env('APP_NAME'),
            "short_name" => env('APP_NAME'),
            "description" => "Hammer CRM",
            "display" => "fullscreen",
            "orientation" => "portrait",
            "background_color" => "white",
            "icons" => [
                ["src" => url("/favicon.png"), "sizes" => "48x48", "type" => "image/png"]
            ],
            "categories" => ['CRM', 'SOFTWARE'],
        ];
        return response()->json($manifest);
    }
}
