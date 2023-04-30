<?php

namespace Tests\Feature\Controllers\Brand;

use Tests\TestCase;

class BrandCreateControllerTest extends TestCase
{
    /** @test */
    public function it_can_create_brand()
    {
        $response = $this->get('/brand/create');

        $response->assertOk();
        $response->assertViewIs('brand.brand');
        $response->assertSee(__('Name'));
    }
}
