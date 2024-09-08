<?php

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

        // Convertir el modelo a array y eliminar 'created_at' y 'updated_at' antes de la aserción
        $brandData = $brand->toArray();
        unset($brandData['created_at'], $brandData['updated_at']);

        $this->assertDatabaseMissing('brand', $brandData);
    }
}
