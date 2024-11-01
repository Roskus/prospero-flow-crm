<?php

namespace Tests\Feature\Helpers;

use App\Helpers\Domain;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DomainTest extends TestCase
{
    #[Test]
    public function it_can_validate_a_domain()
    {
        $urlInvalid = '';
        $validation = Domain::isValid($urlInvalid);
        $this->assertFalse($validation);

        $urlInvalid = 'test';
        $validation = Domain::isValid($urlInvalid);
        $this->assertFalse($validation);

        $urlInvalid = 'www.example.com';
        $validation = Domain::isValid($urlInvalid);
        $this->assertTrue($validation);

        $urlInvalid = 'https://www.example.com';
        $validation = Domain::isValid($urlInvalid);
        $this->assertTrue($validation);

        $urlInvalid = 'https://www.example.com/?test=123';
        $validation = Domain::isValid($urlInvalid);
        $this->assertTrue($validation);
    }
}
