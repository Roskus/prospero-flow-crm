<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanySaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_company(): void
    {
        $data = [
            'name' => fake()->word(),
            'currency' => fake()->bothify('???'),
            'logo' => UploadedFile::fake()->image('logo.jpg'),
        ];

        $response = $this->post('/company/save', $data);

        $logo = Company::all()->last()->logo;
        $data['logo'] = $logo;

        $response->assertRedirect('/company');
        $this->assertDatabaseHas('company', $data);

        $company_folder = Str::slug($data['name'], '_');
        $destination_path = public_path().DIRECTORY_SEPARATOR.'asset'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'company'.DIRECTORY_SEPARATOR.$company_folder;
        $destination = $destination_path.DIRECTORY_SEPARATOR.$logo;

        $this->assertFileExists($destination);

        File::delete($destination);
    }
}
