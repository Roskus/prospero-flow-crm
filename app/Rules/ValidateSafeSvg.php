<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use DOMDocument;
use DOMElement;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ValidateSafeSvg implements ValidationRule
{
    /**
     * Elements that run scripts or embed other documents, by local name so any namespace prefix is caught.
     */
    private const array FORBIDDEN_ELEMENTS = ['script', 'foreignobject', 'iframe', 'object', 'embed', 'handler', 'listener'];

    private const string FAILURE = 'The logo SVG file contains potentially dangerous content and cannot be uploaded.';

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

        if (! $this->hasSafeMarkup($content)) {
            $fail(self::FAILURE);

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
                $fail(self::FAILURE);

                return;
            }
        }
    }

    /**
     * Parses the SVG and checks every element and attribute by local name, so namespaced
     * elements such as <s:script> are caught. Unparseable files and DTD entity declarations are rejected.
     */
    private function hasSafeMarkup(string $content): bool
    {
        if (stripos($content, '<!ENTITY') !== false) {
            return false;
        }

        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadXML($content, LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return false;
        }

        foreach ($document->getElementsByTagName('*') as $element) {
            if (in_array(strtolower($element->localName), self::FORBIDDEN_ELEMENTS, true) || ! $this->hasSafeAttributes($element)) {
                return false;
            }
        }

        return true;
    }

    private function hasSafeAttributes(DOMElement $element): bool
    {
        foreach ($element->attributes as $attribute) {
            $name = strtolower($attribute->localName);
            $value = strtolower((string) preg_replace('/[\s\x00-\x1F]+/', '', $attribute->value));

            if (str_starts_with($name, 'on')
                || str_contains($value, 'javascript:')
                || str_contains($value, 'vbscript:')
                || ($name === 'href' && str_starts_with($value, 'data:'))
                || ($name === 'attributename' && str_ends_with($value, 'href'))) {
                return false;
            }
        }

        return true;
    }
}
