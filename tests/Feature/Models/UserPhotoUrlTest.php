<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserPhotoUrlTest extends TestCase
{
    #[Test]
    public function it_is_null_without_photo(): void
    {
        $user = User::factory()->create(['photo' => null]);

        $this->assertNull($user->photo_url);
    }

    #[Test]
    public function it_points_to_the_user_folder_when_the_photo_is_stored_there(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['photo' => 'avatar.jpg']);
        Storage::disk('public')->put('user/'.$user->id.'/avatar.jpg', 'content');

        $this->assertSame(Storage::disk('public')->url('user/'.$user->id.'/avatar.jpg'), $user->photo_url);
    }

    #[Test]
    public function a_legacy_photo_keeps_the_own_company_folder_after_switching_company(): void
    {
        Storage::fake('public');
        $ownCompany = Company::factory()->create();
        $otherCompany = Company::factory()->create();
        $user = User::factory()->create([
            'photo' => 'legacy.jpg',
            'company_id' => $otherCompany->id,
            'default_company_id' => $ownCompany->id,
        ]);

        $this->assertSame('/asset/upload/company/'.Str::slug($ownCompany->name, '_').'/legacy.jpg', $user->photo_url);
    }

    #[Test]
    public function a_legacy_photo_uses_the_active_company_folder_without_multi_company_access(): void
    {
        Storage::fake('public');
        $company = Company::factory()->create();
        $user = User::factory()->create(['photo' => 'legacy.jpg', 'company_id' => $company->id, 'default_company_id' => null]);

        $this->assertSame('/asset/upload/company/'.Str::slug($company->name, '_').'/legacy.jpg', $user->photo_url);
    }
}
