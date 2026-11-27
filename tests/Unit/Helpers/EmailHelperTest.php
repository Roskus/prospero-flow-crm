<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use App\Helpers\EmailHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailHelperTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function emailProvider(): array
    {
        return [
            'acute accent' => ['damián.pierdominici@example.com', 'damian.pierdominici@example.com'],
            'tilde n' => ['pablo.muñoz@example.com', 'pablo.munoz@example.com'],
            'uppercase accented letter' => ['ing.miguelÁngel@example.com', 'ing.miguelangel@example.com'],
            'several accents' => ['maría.gonzálezsañudo@example.com', 'maria.gonzalezsanudo@example.com'],
            'plain email is lowercased and trimmed' => ['  John.Doe@Example.com ', 'john.doe@example.com'],
            'plain email unchanged' => ['john.doe@example.com', 'john.doe@example.com'],
            'unicode domain is converted to idna' => ['ana@bücher.de', 'ana@xn--bcher-kva.de'],
            'accented local part and uppercase unicode domain' => ['Damián@Bücher.de', 'damian@xn--bcher-kva.de'],
            'idna domain unchanged' => ['ana@xn--bcher-kva.de', 'ana@xn--bcher-kva.de'],
            'value without at sign' => ['Álvaro', 'alvaro'],
        ];
    }

    #[Test]
    #[DataProvider('emailProvider')]
    public function normalize_removes_accents_and_lowercases(string $email, string $expected): void
    {
        $this->assertSame($expected, EmailHelper::normalize($email));
    }
}
