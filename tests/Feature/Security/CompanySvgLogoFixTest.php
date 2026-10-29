<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CompanySvgLogoFixTest extends TestCase
{
    use RefreshDatabase;

    public function test_malicious_svg_with_script_is_rejected(): void
    {
        $company = Company::factory()->create(['name' => 'Test Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('SuperAdmin');

        $maliciousSvg = <<<'SVG'
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">
  <script type="text/javascript">
    alert('XSS');
  </script>
</svg>
SVG;

        $file = UploadedFile::fake()->createWithContent('logo.svg', $maliciousSvg);

        $this->actingAs($user);
        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'EUR',
            'slug' => $company->slug,
            'logo' => $file,
        ]);

        // Should have validation error for logo
        $response->assertSessionHasErrors('logo');

        echo "\n✓ Malicious SVG with <script> tag correctly rejected\n";
        echo "  Validation prevents XSS attack vector\n";
    }

    public function test_svg_with_script_renamed_to_png_is_rejected(): void
    {
        $company = Company::factory()->create(['name' => 'Renamed Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('SuperAdmin');

        $path = tempnam(sys_get_temp_dir(), 'logo');
        file_put_contents($path, '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
        $file = new UploadedFile($path, 'logo.png', null, null, true);

        $this->actingAs($user);
        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'EUR',
            'slug' => $company->slug,
            'logo' => $file,
        ]);

        $response->assertSessionHasErrors('logo');
        $this->assertNull($company->fresh()->logo);
    }

    public function test_svg_with_entity_encoded_javascript_link_is_rejected(): void
    {
        $company = Company::factory()->create(['name' => 'Encoded Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('SuperAdmin');

        $encodedSvg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'
            .'<a xlink:href="&#106;avascript:alert(1)"><text y="20">x</text></a></svg>';
        $file = UploadedFile::fake()->createWithContent('logo.svg', $encodedSvg);

        $this->actingAs($user);
        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'EUR',
            'slug' => $company->slug,
            'logo' => $file,
        ]);

        $response->assertSessionHasErrors('logo');
    }

    public function test_svg_with_foreign_object_is_rejected(): void
    {
        $company = Company::factory()->create(['name' => 'Foreign Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('SuperAdmin');

        $foreignSvg = '<svg xmlns="http://www.w3.org/2000/svg"><foreignObject width="100" height="100">'
            .'<body xmlns="http://www.w3.org/1999/xhtml"><p>x</p></body></foreignObject></svg>';
        $file = UploadedFile::fake()->createWithContent('logo.svg', $foreignSvg);

        $this->actingAs($user);
        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'EUR',
            'slug' => $company->slug,
            'logo' => $file,
        ]);

        $response->assertSessionHasErrors('logo');
    }

    public function test_safe_svg_logo_upload_is_accepted(): void
    {
        $company = Company::factory()->create(['name' => 'Safe SVG Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('SuperAdmin');

        $safeSvg = <<<'SVG'
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">
  <circle cx="50" cy="50" r="40" fill="blue" />
</svg>
SVG;

        $file = UploadedFile::fake()->createWithContent('logo.svg', $safeSvg);

        $this->actingAs($user);
        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'EUR',
            'slug' => $company->slug,
            'logo' => $file,
        ]);

        // Should redirect to company list
        $response->assertRedirect('/company');

        // Verify logo was saved
        $updatedCompany = $company->fresh();
        $this->assertNotNull($updatedCompany->logo);
        $this->assertStringEndsWith('.svg', $updatedCompany->logo);

        echo "\n✓ Safe SVG logo upload correctly accepted\n";
        echo '  Filename: '.$updatedCompany->logo."\n";
    }

    public function test_jpg_logo_upload_is_accepted(): void
    {
        $company = Company::factory()->create(['name' => 'Valid Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('SuperAdmin');

        $file = UploadedFile::fake()->image('logo.jpg', 100, 100);

        $this->actingAs($user);
        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'EUR',
            'slug' => $company->slug,
            'logo' => $file,
        ]);

        // Should redirect to company list
        $response->assertRedirect('/company');

        // Verify logo was saved
        $updatedCompany = $company->fresh();
        $this->assertNotNull($updatedCompany->logo);

        echo "\n✓ JPG logo upload correctly accepted\n";
        echo '  Filename: '.$updatedCompany->logo."\n";
    }

    public function test_png_logo_upload_is_accepted(): void
    {
        $company = Company::factory()->create(['name' => 'PNG Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('SuperAdmin');

        $file = UploadedFile::fake()->image('logo.png', 100, 100, 'png');

        $this->actingAs($user);
        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'EUR',
            'slug' => $company->slug,
            'logo' => $file,
        ]);

        $response->assertRedirect('/company');
        $updatedCompany = $company->fresh();
        $this->assertNotNull($updatedCompany->logo);

        echo "\n✓ PNG logo upload correctly accepted\n";
    }

    public function test_oversized_logo_upload_is_rejected(): void
    {
        $company = Company::factory()->create(['name' => 'Size Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('SuperAdmin');

        // Create a 3MB file (exceeds 2MB limit)
        $file = UploadedFile::fake()->create('logo.jpg', 3072, 'image/jpeg');

        $this->actingAs($user);
        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'EUR',
            'slug' => $company->slug,
            'logo' => $file,
        ]);

        $response->assertSessionHasErrors('logo');

        echo "\n✓ Oversized logo upload correctly rejected\n";
    }
}
