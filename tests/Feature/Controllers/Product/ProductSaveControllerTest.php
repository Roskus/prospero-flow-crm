<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Product;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_a_product(): void
    {
        $product = Product::factory()->make()->toArray();
        $product['photo'] = UploadedFile::fake()->image('photo.jpg');
        $product['expiration_date'] = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->post('product/save', $product);
        $response->assertRedirect('/product');

        $response = $this->get('/product');
        $response->assertSee($product['name']);
    }
}
