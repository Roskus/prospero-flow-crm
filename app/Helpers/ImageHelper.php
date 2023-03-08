<?php

declare(strict_types=1);

namespace App\Helpers;

class ImageHelper
{
    public static function render(string $path): string
    {
        return isset($path) ? 'data:image/png;base64,'.base64_encode(file_get_contents($path)) : '';
    }
}
