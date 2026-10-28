<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductUpdateControllerTest extends TestCase
{
    #[Test]
    public function the_product_form_uses_searchable_category_and_brand_selects(): void
    {
        $category = Category::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Zzbooks']);
        $brand = Brand::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Zzpenguin']);
        $product = Product::factory()->create([
            'company_id' => $this->user->company_id,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ]);

        $response = $this->get('/product/update/'.$product->id);

        $response->assertOk();
        $content = $response->getContent();
        $this->assertMatchesRegularExpression('/<select name="category_id"[^>]*data-tom-select[^>]*required/', $content);
        $this->assertMatchesRegularExpression('/<select name="brand_id"[^>]*data-tom-select[^>]*required/', $content);
        $response->assertSee('<option value="'.$category->id.'" selected>Zzbooks</option>', false);
        $response->assertSee('<option value="'.$brand->id.'" selected>Zzpenguin</option>', false);
        $response->assertSee('href="'.url('/category').'"', false);
        $response->assertSee('href="'.url('/brand').'"', false);
        $response->assertSee('tom-select.complete.min.js', false);
    }

    #[Test]
    public function the_new_product_form_has_no_category_or_brand_selected(): void
    {
        Category::factory()->create(['company_id' => $this->user->company_id]);
        Brand::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/product/create');

        $response->assertOk();
        $this->assertDoesNotMatchRegularExpression('/name="(category|brand)_id".*?\sselected[\s>]/s', $this->selectsOf($response->getContent()));
    }

    private function selectsOf(string $html): string
    {
        preg_match_all('/<select name="(?:category|brand)_id".*?<\/select>/s', $html, $matches);

        return implode("\n", $matches[0]);
    }
}
