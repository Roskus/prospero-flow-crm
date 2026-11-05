<?php

declare(strict_types=1);

namespace App\ValueObjects;

/**
 * Profile path of a LinkedIn member URL (`/in/<slug>`), the same for every way of writing it:
 * with or without scheme, `www.` or a country subdomain, query, fragment, trailing slash or capitals.
 */
final readonly class LinkedinProfileUrl
{
    private function __construct(public string $path) {}

    public static function fromString(?string $url): ?self
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if (! preg_match('#^[a-z][a-z0-9+.-]*://#i', $url)) {
            $url = 'https://'.$url;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host !== 'linkedin.com' && ! str_ends_with($host, '.linkedin.com')) {
            return null;
        }

        $path = rawurldecode((string) parse_url($url, PHP_URL_PATH));
        if (! preg_match('#^/in/([^/]+)/?$#u', $path, $matches)) {
            return null;
        }

        return new self('/in/'.mb_strtolower($matches[1]));
    }
}
