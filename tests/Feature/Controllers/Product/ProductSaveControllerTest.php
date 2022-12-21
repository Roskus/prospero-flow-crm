<?php

namespace Tests\Feature\Controllers\Product;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSaveControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_save_a_product()
    {
        $user = $this->signIn();
        $this->actingAs($user);

        $name = fake()->name();
        $response = $this->post('product/save', ['name' => $name, 'cost' => 1, 'price' => 1]);
        $response->assertRedirect('/product');

        $response = $this->get('/product');
        $response->assertSee($name);
    }
}
