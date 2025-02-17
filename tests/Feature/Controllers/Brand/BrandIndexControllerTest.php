<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Brand;

use App\Models\Brand;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BrandIndexControllerTest extends TestCase
{
    #[Test]
    public function it_can_index_brand()
    {
        $brands = Brand::factory()->count(2)->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/brand');
        $response->assertOk();

        foreach ($brands as $brand) {
            $response->assertSee($brand->name);
        }
    }
}
