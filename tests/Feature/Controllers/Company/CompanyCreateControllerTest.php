<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyCreateControllerTest extends TestCase
{
    #[Test]
    public function it_can_create_company(): void
    {
        $response = $this->get('/company/create');
        $response->assertOk();
        $response->assertViewIs('company.company');
        $response->assertSee(__('Name'));
        $response->assertSee(__('E-mail'));
        $response->assertSee(__('Country'));
    }
}
