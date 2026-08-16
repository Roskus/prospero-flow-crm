<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Security regression tests for the company logo upload.
 *
 * Documents the fix for CWE-434 (Unrestricted Upload) + CWE-79 (Stored XSS):
 * SVG files with embedded <script> are rejected before being stored.
 */
class SvgLogoUploadXssTest extends TestCase
{
    use RefreshDatabase;

    public function test_malicious_svg_logo_upload_is_rejected(): void
    {
        $company = Company::factory()->create(['name' => 'Test Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('SuperAdmin');

        $maliciousSvg = <<<'SVG'
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">
  <script type="text/javascript">
    fetch('/api/test-xss').then(r => r.json()).catch(e => console.log('XSS'));
  </script>
  <circle cx="50" cy="50" r="40" fill="blue" />
</svg>
SVG;

        $file = UploadedFile::fake()->createWithContent('logo.svg', $maliciousSvg);

        $this->actingAs($user);
        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'slug' => $company->slug,
            'logo' => $file,
        ]);

        $response->assertSessionHasErrors('logo');
        $this->assertNull($company->fresh()->logo);
    }

    public function test_malicious_svg_is_not_persisted_as_company_logo(): void
    {
        $company = Company::factory()->create(['name' => 'Public Test']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('SuperAdmin');

        $maliciousSvg = <<<'SVG'
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg">
  <script>alert('XSS')</script>
</svg>
SVG;

        $file = UploadedFile::fake()->createWithContent('logo.svg', $maliciousSvg);

        $this->actingAs($user);
        $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'slug' => $company->slug,
            'logo' => $file,
        ]);

        $this->assertNull($company->fresh()->logo);
    }
}
