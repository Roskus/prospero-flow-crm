<?php

declare(strict_types=1);

namespace Tests\Feature\Helpers;

use App\Helpers\Domain;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[Group('network')]

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

    #[Test]
    public function it_refuses_ip_literals_private_hosts_and_other_schemes(): void
    {
        $this->assertFalse(Domain::isValid('8.8.8.8'));
        $this->assertFalse(Domain::isValid('http://127.0.0.1'));
        $this->assertFalse(Domain::isValid('http://10.0.0.1/admin'));
        $this->assertFalse(Domain::isValid('localhost'));
        $this->assertFalse(Domain::isValid('http://172-23-0-4.sslip.io/ssrf-poc-marker'));
        $this->assertFalse(Domain::isValid('ftp://example.com'));
        $this->assertFalse(Domain::isValid('this-domain-does-not-exist-'.uniqid().'.com'));
    }
}
