<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Brand;

use App\Models\Brand;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BrandUpdateControllerTest extends TestCase
{
    #[Test]
    public function it_can_update_brand(): void
    {
        $brand = Brand::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('brand/update/'.$brand->id);
        $response->assertSee($brand->name);
    }
}
