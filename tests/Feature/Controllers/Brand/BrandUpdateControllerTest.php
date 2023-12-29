<?php

namespace Tests\Feature\Controllers\Brand;

use App\Models\Brand;
use Tests\TestCase;

class BrandUpdateControllerTest extends TestCase
{
    /** @test */
    public function it_can_update_brand(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->get('brand/update/'.$brand->id);
        $response->assertSee($brand->name);
    }
}
