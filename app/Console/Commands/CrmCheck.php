<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CrmCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $errors = [];
        $DS = DIRECTORY_SEPARATOR;
        $path_models = 'Models'.$DS;
        $path_controllers = 'Http'.$DS.'Controllers'.$DS;

        $models = array_map(function ($file) {
            $pattern = '/.*app/';
            $string = $file->getPathname();
            $model = 'App'.substr(preg_replace($pattern, '', $string), 0, -4);

            return $model;
        }, File::allFiles(app_path('Models')));

        /** FILES EXISTS */
        foreach ($models as $model) {
            $model = str_replace('App\\Models\\', '', $model);

            $archivos = [
                /** MODELS */
                app_path($path_models.$model.'.php'),
                /** CONTROLLERS */
                app_path($path_controllers.$model.$DS.$model.'CreateController.php'),
                app_path($path_controllers.$model.$DS.$model.'DeleteController.php'),
                app_path($path_controllers.$model.$DS.$model.'IndexController.php'),
                app_path($path_controllers.$model.$DS.$model.'SaveController.php'),
                app_path($path_controllers.$model.$DS.$model.'ShowController.php'),
                app_path($path_controllers.$model.$DS.$model.'UpdateController.php'),
            ];

            foreach ($archivos as $archivo) {
                if (! File::exists($archivo)) {
                    array_push($errors, 'The file '.preg_replace('/.*app/', '', $archivo).' does not exist.');
                }
            }
            array_push($errors, ' ');
        }

        /** METHODS EXISTS */
        foreach ($models as $model) {
            $methods = ['getAll', 'getAllByCompanyId'];
            foreach ($methods as $method) {
                if (! in_array($method, get_class_methods(new $model))) {
                    array_push($errors, 'The method '.$method.' does not exist in the mode '.$model.'.');
                }
            }

            array_push($errors, ' ');
        }

        dump($errors);

        return Command::SUCCESS;
    }
}
