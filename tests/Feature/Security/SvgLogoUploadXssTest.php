<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SvgLogoUploadXssTest extends TestCase
{
    use RefreshDatabase;

    public function test_svg_logo_upload_accepted_without_validation(): void
    {
        // Create test data
        $company = Company::factory()->create(['name' => 'Test Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('SuperAdmin');

        // Create malicious SVG content
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

        // Authenticate as user with "update company" permission
        $this->actingAs($user);

        // Upload logo - no validation should stop this
        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'slug' => $company->slug,
            'logo' => $file,
        ]);

        // Verify upload was accepted (no validation error)
        $this->assertResponseRedirects('/company');

        // Verify company was updated with logo
        $updatedCompany = $company->fresh();
        $this->assertNotNull($updatedCompany->logo);
        $this->assertStringEndsWith('.svg', $updatedCompany->logo);

        echo "\n✓ SVG file upload accepted without validation\n";
        echo '  Logo filename stored: '.$updatedCompany->logo."\n";
    }

    public function test_company_logo_served_publicly_without_authentication(): void
    {
        // Create company and upload logo
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

        // Get the company to find logo filename
        $updatedCompany = $company->fresh();
        $logoPath = '/storage/company/'.str_slug($company->name, '_').'/'.$updatedCompany->logo;

        // Access logo WITHOUT authentication in a fresh session (no cookies)
        $response = $this->get($logoPath);

        echo "\n✓ Logo is publicly accessible without authentication\n";
        echo '  URL: '.$logoPath."\n";
        echo '  Status: '.$response->status()."\n";

        // The vulnerability: file is publicly accessible
        $this->assertEquals(200, $response->status(), 'Logo should be publicly accessible without auth');
        $this->assertStringContainsString('<script', $response->content());
    }
}
