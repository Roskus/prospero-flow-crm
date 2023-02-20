<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Lead;

class LeadStatus
{
    public static function renderBadge(string $status): string
    {
        return match ($status) {
            Lead::OPEN => 'text-bg-success',
            Lead::IN_PROGRESS => 'text-bg-warning',
            default => 'text-bg-dark',
        };
    }
}
