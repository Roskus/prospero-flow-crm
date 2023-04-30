<?php

namespace Tests\Feature\Controllers\Brand;

use App\Models\Brand;
use Tests\TestCase;

class BrandDeleteControllerTest extends TestCase
{
    /** @test */
    public function it_can_delete_brand()
    {
        $brand = Brand::factory()->create();

        $this->get('brand/delete/'.$brand->id);

        $this->assertDatabaseMissing('brand', $brand->toArray());
    }
}
