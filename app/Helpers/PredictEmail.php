<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;

/**
 * @version 1.0.3
 */
class PredictEmail
{
    private function extractDomain(string $company_url): string
    {
        $parsedUrl = parse_url($company_url);
        $host = $parsedUrl['host'] ?? '';

        // Eliminar posibles 'www.' y devolver solo el dominio y TLD
        return preg_replace('/^www\./', '', $host);
    }

    private function formatEmail(string $email): string
    {
        return strtolower($email);
    }

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
        $host = $this->extractDomain($company_url);

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
        return $this->formatEmail($name.'.'.$last_name.'@'.$host);
    }

    public function firstLetterNameLastName(string $name, string $last_name, string $host): string
    {
        return $this->formatEmail(substr($name, 0, 1).$last_name.'@'.$host);
    }

    public function nameOnly(string $name, string $host): string
    {
        return $this->formatEmail(substr($name, 0, 1).'@'.$host);
    }

    public function firstLettersOfNamesAndLastName(string $name, string $last_name, string $host): string
    {
        $firstLetters = '';
        $names = explode(' ', $name);
        foreach ($names as $n) {
            $firstLetters .= substr($n, 0, 1);
        }
        $last_name = str_replace(' ', '', trim($last_name));

        // Concatena las primeras letras con el apellido, asegurándote de que esté en minúsculas.
        // Nota que aquí también aseguramos que el apellido se añade correctamente sin espacios ni capitalización.
        return $this->formatEmail($firstLetters.$last_name.'@'.$host);
    }

    public function isValid(string $email): bool
    {
        $rules = ['email' => 'email:rfc,dns'];
        $validator = Validator::make(['email' => $email], $rules);

        return $validator->passes();
    }
}
