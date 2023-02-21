<?php

declare(strict_types=1);

namespace App\Helpers;

class EmailStatusHelper
{
    public static function statusCssClass(?string $status): string
    {
        return match ($status) {
            'draft' => 'secondary',
            'sent' => 'success',
            'error' => 'danger',
            null => '',
            default => ''
        };
    }
}
