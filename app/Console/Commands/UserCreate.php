<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {company?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companyId = $this->argument('company');
        if (is_null($companyId)) {
            $companyId = $this->ask('What is the id of the company?');
        }

        $first_name = $this->ask('What is the first name of the user?');
        $last_name = $this->ask('What is the last name of the user?');
        $email = $this->ask('What is the email of the user?');
        $lang = $this->ask('What is the lang of the user? (en/es)');
        $password = Str::random(10);

        $user = new User();
        $user->company_id = $companyId;
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->email = $email;
        $user->lang = $lang;
        $user->password = Hash::make($password);

        // TODO validate here
        $user->save();

        $user->assignRole();

        $this->info('User created successfully');
        $this->info('User temporary password: '.$password);

        return Command::SUCCESS;
    }
}
