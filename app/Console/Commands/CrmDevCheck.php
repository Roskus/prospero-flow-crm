<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CrmDevCheck extends Command
{
    private array $errors = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:dev-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check code consistency, find missing statement. For development purposes only.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $DIR_SEP = DIRECTORY_SEPARATOR;
        $path_models = 'Models'.$DIR_SEP;
        $path_controllers = 'Http'.$DIR_SEP.'Controllers'.$DIR_SEP;

        $models = $this->getModels();

        /** FILES EXISTS */
        foreach ($models as $model_FQDN) {
            $model = str_replace('App\\Models\\', '', $model_FQDN);

            $files = [
                /** MODELS */
                app_path($path_models.$model.'.php'),
                /** CONTROLLERS */
                app_path($path_controllers.$model.$DIR_SEP.$model.'CreateController.php'),
                app_path($path_controllers.$model.$DIR_SEP.$model.'DeleteController.php'),
                app_path($path_controllers.$model.$DIR_SEP.$model.'IndexController.php'),
                app_path($path_controllers.$model.$DIR_SEP.$model.'SaveController.php'),
                app_path($path_controllers.$model.$DIR_SEP.$model.'ShowController.php'),
                app_path($path_controllers.$model.$DIR_SEP.$model.'UpdateController.php'),
            ];

            foreach ($files as $file) {
                if (! File::exists($file)) {
                    $file_name = preg_replace('/.*app/', '', $file);
                    $this->errors[$model_FQDN][] = 'The file '.$file_name.' does not exist.';
                }
            }
        }

        /** METHODS EXISTS */
        foreach ($models as $model_FQDN) {
            $methods = ['getAll', 'getAllByCompanyId'];

            foreach ($methods as $method) {
                if (! in_array($method, get_class_methods(new $model_FQDN))) {
                    $this->errors[$model_FQDN][] = 'The method '.$method.' does not exist, in model '.$model_FQDN.'.';
                }
            }
        }

        foreach ($this->errors as $modelName => $modelErrors) {
            $this->line("##### $modelName #####");
            echo implode(PHP_EOL, $modelErrors).PHP_EOL;
            $this->newLine();
        }

        return Command::SUCCESS;
    }

    private function getModels()
    {
        return array_map(function ($file) {
            $pattern = '/.*app/';
            $string = $file->getPathname();
            $model_FQDN = 'App'.substr(preg_replace($pattern, '', $string), 0, -4);

            return $model_FQDN;
        }, File::allFiles(app_path('Models')));
    }
}
