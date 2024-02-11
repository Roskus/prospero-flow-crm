<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;

/**
 * @version 1.0.2
 */
class PredictEmail
{
    /**
     * @param  string  $name  Person first name
     * @param  string  $last_name  Person last name
     * @param  string  $company_url  Company website
     */
    public function predict(string $name, string $last_name, string $company_url): string|false
    {
        $name = str_replace(' ', '', trim(strtolower($name)));
        $last_name = str_replace(' ', '', trim(strtolower($last_name)));
        $email = false;
        $host = str_replace(['https://', 'http://', 'www.'], '', $company_url);

        // online
        if ($socket = @fsockopen($host, 80, $errno, $errstr, 30)) {
            $cases = [
                'fullNameWithDots',
                'firstLetterNameLastName',
                'nameOnly',
                'firstLettersOfNamesAndLastName',
            ];

            foreach ($cases as $case) {
                $email = $this->$case($name, $last_name, $host);
                if ($this->isValid($email)) {
                    return $email;
                }
            }
        }

        return $email;
    }

    public function fullNameWithDots(string $name, string $last_name, string $host): string
    {
        return strtolower($name.'.'.$last_name.'@'.$host);
    }

    public function firstLetterNameLastName(string $name, string $last_name, string $host): string
    {
        return strtolower(substr($name, 0, 1).$last_name.'@'.$host);
    }

    public function nameOnly(string $name, string $host): string
    {
        return strtolower(substr($name, 0, 1).'@'.$host);
    }

    public function firstLettersOfNamesAndLastName(string $name, string $last_name, string $host): string
    {
        $firstLetters = '';
        $names = explode(' ', $name);
        foreach ($names as $n) {
            $firstLetters .= substr($n, 0, 1);
        }

        return strtolower($firstLetters.$last_name.'@'.$host);
    }

    public function isValid(string $email): bool
    {
        $rules = ['email' => 'email:rfc,dns'];
        $validator = Validator::make(['email' => $email], $rules);

        return $validator->passes();
    }
}
