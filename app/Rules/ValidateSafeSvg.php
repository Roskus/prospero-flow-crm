<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ValidateSafeSvg implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Only validate if it's an SVG file
        if (! $value instanceof UploadedFile || ! str_ends_with(strtolower($value->getClientOriginalName()), '.svg')) {
            return;
        }

        // Read file content
        $content = file_get_contents($value->getRealPath());
        if ($content === false) {
            $fail('The logo file could not be read.');

            return;
        }

        // Detect dangerous patterns that could execute scripts
        $dangerousPatterns = [
            // Script tags
            '/<script[^>]*>/i',
            // Event handlers
            '/on\w+\s*=/i',
            // JavaScript protocol
            '/javascript:/i',
            // iframe or object tags that could load content
            '/<iframe[^>]*>/i',
            '/<object[^>]*>/i',
            '/<embed[^>]*>/i',
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $fail('The logo SVG file contains potentially dangerous content and cannot be uploaded.');

                return;
            }
        }
    }
}
