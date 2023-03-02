<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * PWA
 */
class ManifestController extends Controller
{
    /**
     * https://developer.mozilla.org/es/docs/Web/Manifest
     * Render
     */
    public function renderWebManifest(Request $request, string $locale = 'en'): JsonResponse
    {
        $manifest = [
            'lang' => $locale,
            'name' => env('APP_NAME'),
            'short_name' => env('APP_NAME'),
            'description' => env('APP_DESCRIPTION', ''),
            'display' => 'fullscreen',
            'orientation' => 'portrait',
            'background_color' => 'white',
            'icons' => [
                ['src' => url('/favicon.png'), 'sizes' => '48x48', 'type' => 'image/png'],
            ],
            'categories' => ['CRM', 'SOFTWARE'],
        ];

        return response()->json($manifest);
    }
}
