<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use ZipArchive;

/**
 * Packages browser-extension/ as the zip that Chrome, Edge and Firefox install and their
 * stores accept: manifest.json at the root, without the test page. Uses PHP only, so it
 * runs wherever the CRM runs, without Node or a zip binary.
 *
 * The package takes the CRM version (APP_VERSION, bumped on every commit) as extension
 * version, written only in the zip's manifest: browsers and stores need a higher version on
 * every update, and the repository file is not modified, so a deploy leaves no changes.
 */
class BrowserExtensionPackage extends Command
{
    private const string PACKAGE_PREFIX = 'prospero-flow-crm-extension-';

    /** Paths of browser-extension/ left out of the package. */
    private const array EXCLUDED_PATHS = ['tests/', 'README.md'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:extension:package
        {--output= : Directory for the zip, storage/app/browser-extension by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package the browser extension as a zip to install it or upload it to the browser stores';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $package = $this->package(base_path('browser-extension'));
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return Command::FAILURE;
        }

        $this->info('Extension '.config('app.version')." packaged in {$package}");

        return Command::SUCCESS;
    }

    /**
     * Writes the zip and returns its path.
     *
     * @throws RuntimeException when the extension is incomplete or the zip cannot be written
     */
    private function package(string $source): string
    {
        $manifest = $this->manifest($source);
        $this->ensureLocalesComplete($source);

        $package = $this->outputDirectory().'/'.self::PACKAGE_PREFIX.$manifest['version'].'.zip';
        $zip = new ZipArchive;
        if ($zip->open($package, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException("Cannot write {$package}.");
        }

        foreach ($this->packagedFiles($source) as $relativePath => $file) {
            if ($relativePath === 'manifest.json') {
                $zip->addFromString($relativePath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n");
            } else {
                $zip->addFile($file->getPathname(), $relativePath);
            }
        }
        $zip->close();

        return $package;
    }

    /**
     * The extension manifest with the CRM version.
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException when manifest.json is missing or invalid
     */
    private function manifest(string $source): array
    {
        $manifest = json_decode((string) @file_get_contents($source.'/manifest.json'), true);
        if (! is_array($manifest)) {
            throw new RuntimeException('browser-extension/manifest.json is missing or is not valid JSON.');
        }
        $manifest['version'] = (string) config('app.version');

        return $manifest;
    }

    /**
     * @throws RuntimeException when a locale lacks English messages
     */
    private function ensureLocalesComplete(string $source): void
    {
        $missingKeys = $this->missingLocaleKeys($source);
        if ($missingKeys !== []) {
            $lines = array_map(
                fn (string $locale, array $keys): string => "_locales/{$locale}/messages.json is missing: ".implode(', ', $keys),
                array_keys($missingKeys),
                $missingKeys,
            );

            throw new RuntimeException(implode(PHP_EOL, $lines));
        }
    }

    /**
     * @throws RuntimeException when the directory cannot be created
     */
    private function outputDirectory(): string
    {
        $output = $this->option('output') ?: storage_path('app/browser-extension');
        if (! is_dir($output) && ! mkdir($output, 0755, true) && ! is_dir($output)) {
            throw new RuntimeException("Cannot create {$output}.");
        }

        return rtrim($output, '/');
    }

    /**
     * Files to package by their path inside the zip, sorted, without hidden or excluded files
     * nor earlier packages written inside the folder.
     *
     * @return array<string, SplFileInfo>
     */
    private function packagedFiles(string $source): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            $relativePath = str_replace('\\', '/', substr($file->getPathname(), strlen($source) + 1));
            if ($file->isFile() && ! $this->isExcluded($relativePath)) {
                $files[$relativePath] = $file;
            }
        }
        ksort($files);

        return $files;
    }

    private function isExcluded(string $relativePath): bool
    {
        if (preg_match('#(^|/)\.#', $relativePath) === 1 || str_ends_with($relativePath, '.zip')) {
            return true;
        }

        foreach (self::EXCLUDED_PATHS as $excludedPath) {
            if (str_starts_with($relativePath, $excludedPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Message keys of the English locale that another locale lacks, by locale.
     *
     * @return array<string, list<string>>
     */
    private function missingLocaleKeys(string $source): array
    {
        $locales = [];
        foreach (glob($source.'/_locales/*/messages.json') ?: [] as $file) {
            $locales[basename(dirname($file))] = array_keys(json_decode((string) file_get_contents($file), true) ?: []);
        }

        $missing = [];
        foreach ($locales as $locale => $keys) {
            $lacking = array_values(array_diff($locales['en'] ?? [], $keys));
            if ($lacking !== []) {
                $missing[$locale] = $lacking;
            }
        }

        return $missing;
    }
}
