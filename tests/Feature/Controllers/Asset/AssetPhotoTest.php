<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Asset;

use App\Enums\AssetType;
use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssetPhotoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function craneData(array $overrides = []): array
    {
        return ['category' => 'vehicle', 'type' => 'crane', 'code' => 'GR-1', 'name' => 'Grúa', ...$overrides];
    }

    #[Test]
    public function it_stores_the_photo_privately_and_serves_it_to_the_company(): void
    {
        $this->post('/inventory/save', $this->craneData(['photo' => UploadedFile::fake()->image('grua.jpg')->size(2048)]))
            ->assertSessionHasNoErrors();

        $crane = Asset::firstOrFail();
        $this->assertStringStartsWith('asset/'.$this->user->company_id.'/', $crane->photo_path);
        Storage::disk('local')->assertExists($crane->photo_path);

        $this->get('/inventory/photo/'.$crane->id)->assertOk()->assertHeader('Cache-Control', 'max-age=86400, private');
        $this->get('/inventory/vehicle')->assertSee(route('asset.photo', $crane->id), false);
        $this->get(route('asset.show', $crane->id))->assertSee(route('asset.photo', $crane->id), false)->assertSee('object-fit-contain', false);
    }

    #[Test]
    public function an_asset_without_photo_shows_the_category_placeholder(): void
    {
        $crane = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Crane)->create();

        $this->get('/inventory/vehicle')->assertSee('asset-photo-placeholder', false)->assertDontSee(route('asset.photo', $crane->id), false);
        $this->get('/inventory/photo/'.$crane->id)->assertNotFound();
    }

    #[Test]
    public function a_new_photo_replaces_and_deletes_the_previous_one(): void
    {
        $this->post('/inventory/save', $this->craneData(['photo' => UploadedFile::fake()->image('old.png')]));
        $crane = Asset::firstOrFail();
        $oldPath = $crane->photo_path;

        $this->post('/inventory/save', $this->craneData(['id' => $crane->id, 'photo' => UploadedFile::fake()->image('new.webp')]))
            ->assertSessionHasNoErrors();

        $newPath = $crane->fresh()->photo_path;
        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('local')->assertMissing($oldPath);
        Storage::disk('local')->assertExists($newPath);
    }

    #[Test]
    public function the_photo_can_be_removed(): void
    {
        $this->post('/inventory/save', $this->craneData(['photo' => UploadedFile::fake()->image('grua.jpg')]));
        $crane = Asset::firstOrFail();
        $path = $crane->photo_path;

        $this->post('/inventory/save', $this->craneData(['id' => $crane->id, 'remove_photo' => '1']))->assertSessionHasNoErrors();

        $this->assertNull($crane->fresh()->photo_path);
        Storage::disk('local')->assertMissing($path);
    }

    #[Test]
    public function saving_without_a_new_photo_keeps_the_current_one(): void
    {
        $this->post('/inventory/save', $this->craneData(['photo' => UploadedFile::fake()->image('grua.jpg')]));
        $crane = Asset::firstOrFail();
        $path = $crane->photo_path;

        $this->post('/inventory/save', $this->craneData(['id' => $crane->id, 'name' => 'Renamed']))->assertSessionHasNoErrors();

        $this->assertSame($path, $crane->fresh()->photo_path);
        Storage::disk('local')->assertExists($path);
    }

    #[Test]
    public function invalid_files_are_rejected(): void
    {
        $this->post('/inventory/save', $this->craneData(['photo' => UploadedFile::fake()->create('manual.pdf', 100, 'application/pdf')]))
            ->assertSessionHasErrors('photo');
        $this->post('/inventory/save', $this->craneData(['photo' => UploadedFile::fake()->image('huge.jpg')->size(6000)]))
            ->assertSessionHasErrors('photo');
        $this->post('/inventory/save', $this->craneData(['photo' => UploadedFile::fake()->image('valid.jpg')->size(4000)]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('asset', 1);
    }

    #[Test]
    public function a_file_whose_content_does_not_match_its_extension_is_rejected(): void
    {
        $png = UploadedFile::fake()->image('real.png');
        $disguised = new UploadedFile($png->getPathname(), 'disguised.jpg', 'image/jpeg', null, true);

        $this->post('/inventory/save', $this->craneData(['photo' => $disguised]))->assertSessionHasErrors('photo');
    }
}
