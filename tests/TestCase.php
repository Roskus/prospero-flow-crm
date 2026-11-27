<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public const string TEST_EMAIL = 'test@example.com';

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->freezeTime();

        Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        Role::create(['name' => 'User', 'guard_name' => 'web']);

        $this->user = User::factory()->create();
        $this->user->assignRole('SuperAdmin');

        $this->actingAs($this->user);
    }

    /**
     * A real upload holding a JPEG XL codestream header, so fileinfo detects image/jxl from its content.
     */
    protected function fakeJxlImage(string $name = 'image.jxl'): UploadedFile
    {
        return $this->uploadedFileWithContent($name, "\xff\x0a\xfa\x7f\x01\x90\x08\x06\x01\x00\x48\x00\x4b\x00\x4b\x00");
    }

    /**
     * A real upload whose MIME type is detected from its content rather than its name.
     */
    protected function uploadedFileWithContent(string $name, string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($path, $content);

        return new UploadedFile($path, $name, test: true);
    }
}
