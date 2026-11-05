<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\LinkedinProfileUrl;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LinkedinProfileUrlTest extends TestCase
{
    /**
     * Same cases as browser-extension/tests/tests.html.
     *
     * @return array<string, array{string, ?string}>
     */
    public static function urls(): array
    {
        return [
            'canonical' => ['https://www.linkedin.com/in/jane-doe', '/in/jane-doe'],
            'trailing slash and capitals' => ['https://www.linkedin.com/in/Jane-Doe/', '/in/jane-doe'],
            'no scheme and query' => ['linkedin.com/in/jane-doe?trk=public_profile', '/in/jane-doe'],
            'country subdomain over http' => ['http://es.linkedin.com/in/jane-doe', '/in/jane-doe'],
            'fragment' => ['https://www.linkedin.com/in/jane-doe#experience', '/in/jane-doe'],
            'encoded characters' => ['https://www.linkedin.com/in/jos%C3%A9-p%C3%A9rez/', '/in/josé-pérez'],
            'profile subpage' => ['https://www.linkedin.com/in/jane-doe/details/experience/', null],
            'company page' => ['https://www.linkedin.com/company/acme', null],
            'other site' => ['https://example.com/in/jane-doe', null],
            'look-alike domain' => ['https://notlinkedin.com/in/jane-doe', null],
            'empty' => ['', null],
        ];
    }

    #[Test]
    #[DataProvider('urls')]
    public function it_normalizes_profile_urls(string $url, ?string $expectedPath): void
    {
        $this->assertSame($expectedPath, LinkedinProfileUrl::fromString($url)?->path);
    }

    #[Test]
    public function it_gives_the_percent_encoded_path_linkedin_uses_in_its_links(): void
    {
        $profile = LinkedinProfileUrl::fromString('https://www.linkedin.com/in/martín-gómez-pmp®-1a2b3c4d');

        $this->assertSame('/in/mart%C3%ADn-g%C3%B3mez-pmp%C2%AE-1a2b3c4d', $profile?->encodedPath());
        $this->assertSame('/in/jane-doe', LinkedinProfileUrl::fromString('linkedin.com/in/Jane-Doe')?->encodedPath());
    }
}
