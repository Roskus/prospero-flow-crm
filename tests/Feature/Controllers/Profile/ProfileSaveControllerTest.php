<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Profile;

use App\Http\Middleware\MustChangePassword;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_not_save_profile_without_data()
    {
        $response = $this->post('profile/save', []);
        $response->assertSessionHasErrors();
    }

    #[Test]
    public function it_can_save_profile()
    {
        $data = [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => fake()->email(),
            'lang' => 'en',
        ];
        $response = $this->post('profile/save', $data);
        $response->assertRedirect('/profile');

        $response = $this->get('/profile');
        $response->assertSee($data['first_name']);
        $response->assertSee($data['last_name']);
        $response->assertSee($data['email']);
    }

    #[Test]
    public function it_can_save_a_webp_profile_photo(): void
    {
        $this->post('profile/save', [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'lang' => 'en',
            'photo' => UploadedFile::fake()->image('avatar.webp'),
        ])->assertSessionHasNoErrors()->assertRedirect('/profile');

        $this->assertStringEndsWith('.webp', (string) $this->user->fresh()->photo);
    }

    #[Test]
    public function it_can_save_a_jxl_profile_photo(): void
    {
        $this->post('profile/save', [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'lang' => 'en',
            'photo' => $this->fakeJxlImage('avatar.jxl'),
        ])->assertSessionHasNoErrors()->assertRedirect('/profile');

        $this->assertStringEndsWith('.jxl', (string) $this->user->fresh()->photo);
    }

    #[Test]
    public function it_saves_the_photo_in_the_user_folder_after_switching_company(): void
    {
        Storage::fake('public');
        $ownCompanyId = $this->user->company_id;
        $otherCompany = Company::factory()->create();
        $this->user->forceFill(['default_company_id' => $ownCompanyId, 'company_id' => $otherCompany->id])->save();

        $this->post('profile/save', [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'lang' => 'en',
            'photo' => UploadedFile::fake()->image('avatar.jpg'),
        ])->assertSessionHasNoErrors()->assertRedirect('/profile');

        $user = $this->user->fresh();
        Storage::disk('public')->assertExists('user/'.$user->id.'/'.$user->photo);
        $this->assertSame(Storage::disk('public')->url('user/'.$user->id.'/'.$user->photo), $user->photo_url);
        $this->get('/profile')->assertOk()->assertSee($user->photo_url, false);
    }

    #[Test]
    public function a_new_photo_deletes_the_previous_one(): void
    {
        Storage::fake('public');
        $previousPath = 'user/'.$this->user->id.'/previous.jpg';
        Storage::disk('public')->put($previousPath, 'content');
        $this->user->forceFill(['photo' => 'previous.jpg'])->save();

        $this->post('profile/save', [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'lang' => 'en',
            'photo' => UploadedFile::fake()->image('avatar.png'),
        ])->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing($previousPath);
        Storage::disk('public')->assertExists('user/'.$this->user->id.'/'.$this->user->fresh()->photo);
    }

    #[Test]
    public function a_new_photo_deletes_the_previous_legacy_photo(): void
    {
        Storage::fake('public');
        $company = Company::factory()->create(['name' => 'Legacy Photo '.Str::random(8)]);
        $this->user->forceFill(['company_id' => $company->id, 'default_company_id' => null, 'photo' => 'legacy.jpg'])->save();
        $legacyFolder = public_path('asset/upload/company/'.Str::slug($company->name, '_'));
        File::ensureDirectoryExists($legacyFolder);
        File::put($legacyFolder.'/legacy.jpg', 'content');

        try {
            $this->post('profile/save', [
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'lang' => 'en',
                'photo' => UploadedFile::fake()->image('avatar.png'),
            ])->assertSessionHasNoErrors();

            $this->assertFileDoesNotExist($legacyFolder.'/legacy.jpg');
            Storage::disk('public')->assertExists('user/'.$this->user->id.'/'.$this->user->fresh()->photo);
        } finally {
            File::deleteDirectory($legacyFolder);
        }
    }

    #[Test]
    public function it_blocks_polyglot_photo_upload_and_logs_security_incident(): void
    {
        Event::fake([MessageLogged::class]);

        $payload = "GIF89a\n<html><body><script>alert('xss')</script></body></html>";
        $file = UploadedFile::fake()->createWithContent('poc.html', $payload)->mimeType('image/gif');

        $response = $this->post('profile/save', [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => fake()->email(),
            'lang' => 'en',
            'photo' => $file,
        ]);

        $response->assertSessionHasErrors('photo');
        $this->assertNull($this->user->fresh()->photo);

        Event::assertDispatched(MessageLogged::class, function (MessageLogged $event) {
            return ($event->context['channel'] ?? null) === 'security'
                && ($event->context['event'] ?? null) === 'upload_extension_mismatch';
        });
    }

    #[Test]
    public function it_can_update_password()
    {
        $oldPassword = $this->user->password;
        $newPassword = fake()->password(8);
        $data = [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => fake()->email(),
            'lang' => 'en',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ];
        $response = $this->post('profile/save', $data);

        $this->assertNotEquals($oldPassword, User::first()->password);
    }

    #[Test]
    public function it_clears_must_change_password_when_password_provided(): void
    {
        $this->user->must_change_password = true;
        $this->user->save();

        $newPassword = 'NewPass123!';

        $this->withoutMiddleware(MustChangePassword::class)
            ->post('profile/save', [
                'first_name' => fake()->name(),
                'last_name' => fake()->name(),
                'email' => fake()->email(),
                'lang' => 'en',
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]);

        $this->user->refresh();
        $this->assertFalse($this->user->must_change_password);
    }

    #[Test]
    public function it_preserves_must_change_password_when_password_empty(): void
    {
        $this->user->must_change_password = true;
        $this->user->save();

        $this->withoutMiddleware(MustChangePassword::class)
            ->post('profile/save', [
                'first_name' => fake()->name(),
                'last_name' => fake()->name(),
                'email' => fake()->email(),
                'lang' => 'en',
            ]);

        $this->user->refresh();
        $this->assertTrue($this->user->must_change_password);
    }
}
