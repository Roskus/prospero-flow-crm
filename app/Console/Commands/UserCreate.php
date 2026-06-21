<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\UserCreateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:user:create {company?}';

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
        $first_name = null;
        $last_name = null;
        $email = null;
        $lang = null;
        $role = null;

        $availableRoles = Role::pluck('name')->toArray();
        if (empty($availableRoles)) {
            $this->error('No roles found in the system. Please create roles first.');

            return Command::FAILURE;
        }

        while (true) {
            if (is_null($companyId)) {
                $companyId = $this->ask('What is the id of the company?');
            }

            if (is_null($first_name)) {
                $first_name = $this->ask('What is the first name of the user?');
            }

            if (is_null($last_name)) {
                $last_name = $this->ask('What is the last name of the user?');
            }

            if (is_null($email)) {
                $email = $this->ask('What is the email of the user?');
            }

            if (is_null($lang)) {
                $availableLangs = array_keys(config('app.locales', ['en' => 'English', 'es' => 'Spanish']));
                $lang = $this->choice('Select user language', $availableLangs);
            }

            if (is_null($role)) {
                $role = $this->choice('Select user role', $availableRoles);
            }

            $availableLangsStr = implode(',', $availableLangs);
            $validator = Validator::make([
                'company_id' => $companyId,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'lang' => $lang,
                'role' => $role,
            ], [
                'company_id' => 'required|exists:company,id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:user,email',
                'lang' => 'required|in:' . $availableLangsStr,
                'role' => 'required|in:' . implode(',', $availableRoles),
            ]);

            if ($validator->fails()) {
                $this->error('Validation failed:');
                foreach ($validator->errors()->all() as $error) {
                    $this->error('  - '.$error);
                }

                $this->line('');
                $companyId = null;
                $first_name = null;
                $last_name = null;
                $email = null;
                $lang = null;
                $role = null;

                continue;
            }

            break;
        }

        $passwordOption = $this->choice('Generate random password or set your own?', ['generate', 'set'], 0);

        if ($passwordOption === 'generate') {
            $password = Str::random(10);
            $sendEmail = true;
        } else {
            $password = $this->secret('Enter the password for the user');
            $sendEmail = $this->confirm('Send password via email?', false);
        }

        try {
            $userCreateService = new UserCreateService();
            $user = $userCreateService->create([
                'company_id' => $companyId,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'lang' => $lang,
                'password' => $password,
                'role' => $role,
            ], $sendEmail);

            if (! $user || ! $user->id) {
                $this->error('Failed to create user');

                return Command::FAILURE;
            }

            $this->info('User created successfully');
            $this->info('User ID: '.$user->id);
            $this->info('Email: '.$user->email);
            if ($passwordOption === 'generate') {
                $this->info('Temporary password sent via email');
            } elseif ($sendEmail) {
                $this->info('Password sent via email');
            } else {
                $this->info('Password set securely');
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error creating user: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
