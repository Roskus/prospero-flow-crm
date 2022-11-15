<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportProductFromOpenCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-product:opencart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from OpenCart';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;
    }
}
