<?php

namespace App\Helpers;

use App\Enums\LeadStatus as LeadStatusEnum;

class LeadStatus
{
    public static function renderBadge(string $status): string
    {
        $badge = '';
        switch ($status) {
            case LeadStatusEnum::Open->value:
                $badge = 'text-bg-success';
                break;
            case LeadStatusEnum::InProgress->value:
                $badge = 'text-bg-warning';
                break;
            default:
                $badge = 'text-bg-dark';
        }

        return $badge;
    }
}
