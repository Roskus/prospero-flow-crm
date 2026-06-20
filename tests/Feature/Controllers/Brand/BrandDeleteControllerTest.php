<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Brand;

use App\Models\Brand;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BrandDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_brand()
    {
        $brand = Brand::factory()->create();

        $this->get('brand/delete/'.$brand->id);

        $this->assertSoftDeleted('brand', ['id' => $brand->id]);
    }
}
