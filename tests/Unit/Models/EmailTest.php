<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Email;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailTest extends TestCase
{
    /**
     * @return array<string, array{mixed, bool}>
     */
    public static function signatureProvider(): array
    {
        return [
            'null' => [null, false],
            'false' => [false, false],
            'zero' => [0, false],
            'zero string' => ['0', false],
            'true' => [true, true],
            'one' => [1, true],
            'one string' => ['1', true],
        ];
    }

    #[Test]
    #[DataProvider('signatureProvider')]
    public function has_signature_returns_a_boolean(mixed $signature, bool $expected): void
    {
        $email = new Email;
        $email->setRawAttributes(['signature' => $signature]);

        $this->assertSame($expected, $email->hasSignature());
    }
}
