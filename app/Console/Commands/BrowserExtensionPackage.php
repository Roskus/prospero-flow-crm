<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use ZipArchive;

/**
 * Packages browser-extension/ as the zip that Chrome, Edge and Firefox install and their
 * stores accept: manifest.json at the root, without the test page. Uses PHP only, so it
 * runs wherever the CRM runs, without Node or a zip binary.
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
        $source = base_path('browser-extension');
        $manifest = json_decode((string) @file_get_contents($source.'/manifest.json'), true);
        if (! is_array($manifest) || empty($manifest['version'])) {
            $this->error('browser-extension/manifest.json is missing or has no version.');

            return Command::FAILURE;
        }

        $missingKeys = $this->missingLocaleKeys($source);
        if ($missingKeys !== []) {
            foreach ($missingKeys as $locale => $keys) {
                $this->error("_locales/{$locale}/messages.json is missing: ".implode(', ', $keys));
            }

            return Command::FAILURE;
        }

        $output = $this->option('output') ?: storage_path('app/browser-extension');
        if (! is_dir($output) && ! mkdir($output, 0755, true) && ! is_dir($output)) {
            $this->error("Cannot create {$output}.");

            return Command::FAILURE;
        }

        $package = rtrim($output, '/').'/'.self::PACKAGE_PREFIX.$manifest['version'].'.zip';
        $zip = new ZipArchive;
        if ($zip->open($package, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error("Cannot write {$package}.");

            return Command::FAILURE;
        }

        foreach ($this->packagedFiles($source) as $relativePath => $file) {
            $zip->addFile($file->getPathname(), $relativePath);
        }
        $zip->close();

        $this->info("Extension {$manifest['version']} packaged in {$package}");

        return Command::SUCCESS;
    }

    /**
     * Files to package by their path inside the zip, sorted, without hidden or excluded files.
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
        if (preg_match('#(^|/)\.#', $relativePath) === 1) {
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
