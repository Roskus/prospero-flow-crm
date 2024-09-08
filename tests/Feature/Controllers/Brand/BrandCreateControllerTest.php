<?php

namespace Tests\Feature\Controllers\Brand;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BrandCreateControllerTest extends TestCase
{
    #[Test]
    public function it_can_create_brand()
    {
        $response = $this->get('/brand/create');

        $response->assertOk();
        $response->assertViewIs('brand.brand');
        $response->assertSee(__('Name'));
    }
}
