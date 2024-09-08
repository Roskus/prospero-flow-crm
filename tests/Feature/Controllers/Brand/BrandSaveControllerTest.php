<?php

namespace Tests\Feature\Controllers\Brand;

use App\Models\Brand;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BrandSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_brand(): void
    {
        $data = Brand::factory()->create(['company_id' => $this->user->company_id])->toArray();

        $response = $this->post('brand/save', $data);

        $response->assertRedirect('/brand');
        $this->equalTo(Brand::all()->last(), $data);
    }

    #[Test]
    public function it_can_update_brand(): void
    {
        $data = Brand::factory()->create(['company_id' => $this->user->company_id])->toArray();
        unset($data['id']);

        $response = $this->post('brand/save', $data);

        $response->assertRedirect('/brand');
        $this->equalTo(Brand::all()->last(), $data);
    }
}
