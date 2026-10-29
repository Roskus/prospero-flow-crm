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
        if (! $value instanceof UploadedFile) {
            return;
        }

        // Read file content
        $content = file_get_contents($value->getRealPath());
        if ($content === false) {
            $fail('The logo file could not be read.');

            return;
        }

        // Inspect by content, not by file name: an SVG renamed to .png is still an SVG
        if (stripos($content, '<svg') === false) {
            return;
        }

        // Decode entities so payloads such as &#106;avascript: are caught
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5 | ENT_XML1, 'UTF-8');

        // Detect dangerous patterns that could execute scripts
        $dangerousPatterns = [
            // Script tags
            '/<script[^>]*>/i',
            // Event handlers
            '/on\w+\s*=/i',
            // JavaScript protocol
            '/j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:/i',
            // HTML embedded in SVG and data: URIs in links
            '/<foreignObject[^>]*>/i',
            '/href\s*=\s*["\']?\s*data:/i',
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
