<?php

declare(strict_types=1);

namespace Tests\Feature\Translations;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Finder\SplFileInfo;
use Tests\TestCase;

class RrhhTranslationTest extends TestCase
{
    #[Test]
    public function every_rrhh_translation_key_exists_in_english_and_spanish(): void
    {
        $files = [
            ...File::allFiles(resource_path('views/rrhh')),
            ...File::allFiles(app_path('Http/Controllers/Rrhh')),
        ];

        $keys = collect($files)
            ->flatMap(function (SplFileInfo $file): array {
                preg_match_all('/__\(\s*([\'"])(.*?)(?<!\\\\)\1\s*[,)]/', $file->getContents(), $matches);

                return $matches[2];
            })
            ->unique()
            ->values();

        $this->assertNotEmpty($keys);

        foreach (['en', 'es'] as $locale) {
            $translations = json_decode(File::get(lang_path($locale.'.json')), true);
            $missingKeys = $keys->reject(fn (string $key): bool => array_key_exists($key, $translations))->values()->all();

            $this->assertSame([], $missingKeys, "Missing {$locale} translations");
        }
    }

    #[Test]
    public function time_off_request_screen_is_rendered_in_spanish(): void
    {
        $this->user->update(['lang' => 'es']);

        $response = $this->get('/rrhh/time-off/create');

        $response->assertOk();
        $response->assertSeeText('Nueva solicitud de ausencia');
        $response->assertSeeText('Vacaciones');
        $response->assertSeeText('Baja por enfermedad');
        $response->assertDontSeeText('New Time Off Request');
    }
}
