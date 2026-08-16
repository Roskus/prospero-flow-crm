<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\SecurityLogger;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ExtensionMatchesContent implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            return;
        }

        $clientExtension = $this->normalize($value->getClientOriginalExtension());
        $contentExtension = $this->normalize($value->guessExtension());

        if ($clientExtension === '' || $clientExtension !== $contentExtension) {
            app(SecurityLogger::class)->log('upload_extension_mismatch', [
                'attribute' => $attribute,
                'original_name' => $value->getClientOriginalName(),
                'client_extension' => $clientExtension,
                'content_extension' => $contentExtension,
                'mime' => $value->getMimeType(),
                'size' => $value->getSize(),
            ]);

            $fail('The :attribute file name extension must match its actual content.');
        }
    }

    private function normalize(string $extension): string
    {
        $extension = strtolower($extension);

        return match ($extension) {
            'jpeg' => 'jpg',
            default => $extension,
        };
    }
}
