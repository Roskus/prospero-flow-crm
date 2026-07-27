<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserCreateService
{
    public function create(array $data, bool $sendEmail = false): User
    {
        $role = $data['role'] ?? null;
        unset($data['role']);

        $data['password'] = Hash::make($data['password']);
        $data['must_change_password'] = true;

        $user = User::create($data);

        if ($role) {
            $user->assignRole($role);
        } else {
            $user->assignRole('User');
        }

        if ($sendEmail) {
            $this->sendWelcomeEmail($user, $data['password']);
        }

        return $user;
    }

    private function sendWelcomeEmail(User $user, string $plainPassword): void
    {
        Mail::raw(
            "Welcome {$user->first_name}!\n\nYour temporary password is: {$plainPassword}\n\nYou will be required to change it on your first login.",
            function ($message) use ($user) {
                $message->to($user->email)
                    ->subject("Welcome to Prospero Flow CRM, {$user->first_name}");
            }
        );
    }
}
