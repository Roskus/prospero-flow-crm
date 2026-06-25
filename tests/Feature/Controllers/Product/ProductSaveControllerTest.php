<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
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

    #[Test]
    public function it_prevents_viewing_products_from_other_companies(): void
    {
        $userA = $this->user;
        $productA = Product::factory()->create(['company_id' => $userA->company_id]);

        $userB = User::factory()->create();
        $role = Role::findByName('SuperAdmin');
        $userB->assignRole($role);

        $this->actingAs($userB);
        $response = $this->get("/product/update/{$productA->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_prevents_updating_products_from_other_companies(): void
    {
        $userA = $this->user;
        $productA = Product::factory()->create(['company_id' => $userA->company_id]);

        $userB = User::factory()->create();
        $role = Role::findByName('SuperAdmin');
        $userB->assignRole($role);

        $this->actingAs($userB);
        $updateData = [
            'id' => $productA->id,
            'name' => 'Hijacked Product',
            'cost' => 100.00,
            'price' => 200.00,
            'category_id' => $productA->category_id,
            'brand_id' => $productA->brand_id ?? 1,
            'quantity' => 10,
            'min_stock_quantity' => 5,
            'expiration_date' => Carbon::tomorrow()->format('Y-m-d'),
        ];

        $response = $this->post('/product/save', $updateData);
        $response->assertNotFound();

        $productA->refresh();
        $this->assertNotEquals('Hijacked Product', $productA->name);
        $this->assertEquals($userA->company_id, $productA->company_id);
    }
}
