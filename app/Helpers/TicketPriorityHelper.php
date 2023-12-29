<?php

declare(strict_types=1);

namespace App\Helpers;

class TicketPriorityHelper
{
    public static function priorityCssClass(?string $status): string
    {
        return match ($status) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            null => '',
            default => ''
        };
    }
}
