<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use ZipArchive;

class BrowserExtensionPackageTest extends TestCase
{
    private string $output;

    protected function setUp(): void
    {
        parent::setUp();
        $this->output = sys_get_temp_dir().'/extension-package-'.uniqid();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->output);
        parent::tearDown();
    }

    #[Test]
    public function it_packages_the_extension_with_the_manifest_at_the_root(): void
    {
        $version = json_decode((string) file_get_contents(base_path('browser-extension/manifest.json')), true)['version'];
        $package = $this->output.'/prospero-flow-crm-extension-'.$version.'.zip';

        $this->artisan('crm:extension:package', ['--output' => $this->output])
            ->expectsOutputToContain($package)
            ->assertSuccessful();

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($package));
        $names = [];
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $names[] = $zip->getNameIndex($index);
        }
        $manifest = json_decode((string) $zip->getFromName('manifest.json'), true);
        $zip->close();

        $this->assertSame($version, $manifest['version']);
        foreach (['background.js', 'content.js', 'linkedin.js', 'panel.html', 'popup.html', '_locales/en/messages.json', '_locales/es/messages.json', 'icons/icon-128.png'] as $file) {
            $this->assertContains($file, $names);
        }
        $this->assertEmpty(array_filter($names, fn (string $name): bool => str_starts_with($name, 'tests/')));
        $this->assertNotContains('README.md', $names);
    }

    #[Test]
    public function every_locale_has_the_english_messages(): void
    {
        $english = array_keys(json_decode((string) file_get_contents(base_path('browser-extension/_locales/en/messages.json')), true));
        $spanish = array_keys(json_decode((string) file_get_contents(base_path('browser-extension/_locales/es/messages.json')), true));

        $this->assertEqualsCanonicalizing($english, $spanish);
    }
}
