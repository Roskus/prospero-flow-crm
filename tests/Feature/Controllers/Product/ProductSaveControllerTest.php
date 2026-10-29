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
    public function the_new_product_form_defaults_min_stock_quantity_to_zero(): void
    {
        $this->get('/product/create')
            ->assertOk()
            ->assertSee('name="min_stock_quantity" id="min_stock_quantity" value="0"', false);
    }

    #[Test]
    public function it_saves_min_stock_quantity_as_zero_when_omitted(): void
    {
        $product = Product::factory()->make()->toArray();
        unset($product['min_stock_quantity'], $product['photo']);
        $product['expiration_date'] = Carbon::tomorrow()->format('Y-m-d');

        $this->post('product/save', $product)->assertRedirect('/product');

        $this->assertDatabaseHas('product', [
            'name' => $product['name'],
            'min_stock_quantity' => 0,
        ]);
    }

    #[Test]
    public function it_rejects_a_negative_min_stock_quantity(): void
    {
        $product = Product::factory()->make(['min_stock_quantity' => -1])->toArray();

        $this->post('product/save', $product)->assertSessionHasErrors('min_stock_quantity');
    }

    #[Test]
    public function it_saves_weight_and_dimensions_with_their_units(): void
    {
        $product = $this->validProductData([
            'weight' => 1.25,
            'weight_unit' => 'kg',
            'dimensions' => '20 x 15 x 10',
            'dimension_unit' => 'cm',
        ]);

        $this->post('product/save', $product)->assertRedirect('/product');

        $this->assertDatabaseHas('product', [
            'name' => $product['name'],
            'weight' => 1.25,
            'weight_unit' => 'kg',
            'dimensions' => '20 x 15 x 10',
            'dimension_unit' => 'cm',
        ]);
    }

    #[Test]
    public function it_saves_a_product_without_weight_and_dimensions_defaulting_units_to_grams_and_millimeters(): void
    {
        $product = $this->validProductData();
        unset($product['weight'], $product['weight_unit'], $product['dimensions'], $product['dimension_unit']);

        $this->post('product/save', $product)->assertRedirect('/product');

        $this->assertDatabaseHas('product', [
            'name' => $product['name'],
            'weight' => null,
            'weight_unit' => 'g',
            'dimensions' => null,
            'dimension_unit' => 'mm',
        ]);
    }

    #[Test]
    public function it_rejects_an_invalid_weight_unit(): void
    {
        $this->post('product/save', $this->validProductData(['weight_unit' => 'ton']))
            ->assertSessionHasErrors('weight_unit');
    }

    #[Test]
    public function it_saves_volume_with_its_unit(): void
    {
        $product = $this->validProductData(['volume' => 1.5, 'volume_unit' => 'l']);

        $this->post('product/save', $product)->assertRedirect('/product');

        $this->assertDatabaseHas('product', [
            'name' => $product['name'],
            'volume' => 1.5,
            'volume_unit' => 'l',
        ]);
    }

    #[Test]
    public function it_saves_a_product_without_volume_defaulting_the_unit_to_milliliters(): void
    {
        $product = $this->validProductData();
        unset($product['volume'], $product['volume_unit']);

        $this->post('product/save', $product)->assertRedirect('/product');

        $this->assertDatabaseHas('product', [
            'name' => $product['name'],
            'volume' => null,
            'volume_unit' => 'ml',
        ]);
    }

    #[Test]
    public function it_accepts_milligrams_as_weight_unit(): void
    {
        $product = $this->validProductData(['weight' => 250, 'weight_unit' => 'mg']);

        $this->post('product/save', $product)->assertRedirect('/product');

        $this->assertDatabaseHas('product', ['name' => $product['name'], 'weight_unit' => 'mg']);
    }

    #[Test]
    public function it_rejects_an_invalid_volume_unit(): void
    {
        $this->post('product/save', $this->validProductData(['volume_unit' => 'gal']))
            ->assertSessionHasErrors('volume_unit');
    }

    #[Test]
    public function it_rejects_a_negative_volume(): void
    {
        $this->post('product/save', $this->validProductData(['volume' => -1]))
            ->assertSessionHasErrors('volume');
    }

    #[Test]
    public function it_saves_a_product_with_a_webp_photo(): void
    {
        $product = $this->validProductData();
        $product['photo'] = UploadedFile::fake()->image('photo.webp');

        $this->post('product/save', $product)
            ->assertSessionHasNoErrors()
            ->assertRedirect('/product');

        $this->assertStringEndsWith('.webp', Product::where('name', $product['name'])->firstOrFail()->photo);
    }

    #[Test]
    public function it_rejects_a_bmp_photo(): void
    {
        $product = $this->validProductData();
        $product['photo'] = UploadedFile::fake()->image('photo.bmp');

        $this->post('product/save', $product)->assertSessionHasErrors('photo');
    }

    #[Test]
    public function the_product_form_photo_input_accepts_webp(): void
    {
        $this->get('/product/create')
            ->assertOk()
            ->assertSee('accept="image/jpeg,image/png,image/gif,image/webp"', false);
    }

    #[Test]
    public function it_rejects_weight_and_volume_with_more_than_three_decimals(): void
    {
        $this->post('product/save', $this->validProductData(['weight' => 1.2345, 'volume' => 0.0001]))
            ->assertSessionHasErrors(['weight', 'volume']);
    }

    #[Test]
    public function it_accepts_integer_and_three_decimal_weight_and_volume(): void
    {
        $product = $this->validProductData(['weight' => 2, 'volume' => 0.125]);

        $this->post('product/save', $product)->assertRedirect('/product');

        $this->assertDatabaseHas('product', ['name' => $product['name'], 'weight' => 2, 'volume' => 0.125]);
    }

    #[Test]
    public function it_keeps_existing_measurements_when_an_update_omits_them(): void
    {
        $product = Product::factory()->create([
            'company_id' => $this->user->company_id,
            'weight' => 1.5,
            'weight_unit' => 'kg',
            'volume' => 750,
            'volume_unit' => 'ml',
            'dimensions' => '20 x 15 x 10',
            'dimension_unit' => 'cm',
        ]);

        $this->post('product/save', [
            'id' => $product->id,
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'name' => 'Renamed product',
            'cost' => 10,
            'price' => 20,
            'quantity' => 5,
        ])->assertRedirect('/product');

        $this->assertDatabaseHas('product', [
            'id' => $product->id,
            'name' => 'Renamed product',
            'weight' => 1.5,
            'weight_unit' => 'kg',
            'volume' => 750,
            'volume_unit' => 'ml',
            'dimensions' => '20 x 15 x 10',
            'dimension_unit' => 'cm',
        ]);
    }

    #[Test]
    public function it_clears_measurements_when_an_update_sends_them_empty(): void
    {
        $product = Product::factory()->create([
            'company_id' => $this->user->company_id,
            'weight' => 1.5,
            'weight_unit' => 'kg',
            'dimensions' => '20 x 15 x 10',
            'dimension_unit' => 'cm',
        ]);

        $this->post('product/save', [
            'id' => $product->id,
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'name' => $product->name,
            'cost' => 10,
            'price' => 20,
            'quantity' => 5,
            'weight' => '',
            'weight_unit' => '',
            'dimensions' => '',
            'dimension_unit' => '',
        ])->assertRedirect('/product');

        $this->assertDatabaseHas('product', [
            'id' => $product->id,
            'weight' => null,
            'weight_unit' => 'g',
            'dimensions' => null,
            'dimension_unit' => 'mm',
        ]);
    }

    #[Test]
    public function it_rejects_an_invalid_dimension_unit(): void
    {
        $this->post('product/save', $this->validProductData(['dimension_unit' => 'km']))
            ->assertSessionHasErrors('dimension_unit');
    }

    #[Test]
    public function it_rejects_a_negative_weight(): void
    {
        $this->post('product/save', $this->validProductData(['weight' => -1]))
            ->assertSessionHasErrors('weight');
    }

    #[Test]
    public function it_rejects_dimensions_longer_than_100_characters(): void
    {
        $this->post('product/save', $this->validProductData(['dimensions' => str_repeat('a', 101)]))
            ->assertSessionHasErrors('dimensions');
    }

    #[Test]
    public function the_new_product_form_shows_weight_and_dimensions_fields(): void
    {
        $response = $this->get('/product/create')
            ->assertOk()
            ->assertSee('name="weight"', false)
            ->assertSee('name="dimensions"', false)
            ->assertSee('name="dimension_unit"', false)
            ->assertSee('name="volume"', false)
            ->assertSee('name="volume_unit"', false);

        $this->assertMatchesRegularExpression('/<option value="g"\s+selected/', $response->getContent());
        $this->assertMatchesRegularExpression('/<option value="mm"\s+selected/', $response->getContent());
        $this->assertMatchesRegularExpression('/<option value="ml"\s+selected/', $response->getContent());
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validProductData(array $overrides = []): array
    {
        $product = Product::factory()->make($overrides)->toArray();
        unset($product['photo']);
        $product['expiration_date'] = Carbon::tomorrow()->format('Y-m-d');

        return $product;
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
