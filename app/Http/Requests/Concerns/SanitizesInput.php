<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

trait SanitizesInput
{
    /**
     * Exclude fields from sanitization
     * @return array<string>
     */
    protected function unsafeFields(): array
    {
        return [];
    }

    public function passedValidation(): void
    {
        $unsafe = $this->unsafeFields();
        $sanitized = [];

        foreach ($this->all() as $key => $value) {
            if (is_string($value) && ! in_array($key, $unsafe, true)) {
                $sanitized[$key] = strip_tags($value);
            }
        }

        if ($sanitized !== []) {
            $this->merge($sanitized);
        }
    }
}
