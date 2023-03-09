<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Product;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductSaveControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_save_a_product(): void
    {
        $user = $this->signIn();
        $this->actingAs($user);

        $product = Product::factory()->make()->toArray();
        $product['photo'] = UploadedFile::fake()->image('photo.jpg');
        $product['expiration_date'] = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->post('product/save', $product);
        $response->assertRedirect('/product');

        $response = $this->get('/product');
        $response->assertSee($product['name']);
    }
}
