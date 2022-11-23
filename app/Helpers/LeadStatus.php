<?php

namespace App\Helpers;

use App\Models\Lead;

class LeadStatus
{
    public static function renderBadge(string $status): string
    {
        $badge = '';
        switch ($status) {
            case Lead::OPEN:
                $badge = 'text-bg-success';
                break;
            case Lead::IN_PROGRESS:
                $badge = 'text-bg-warning';
                break;
            default:
                $badge = 'text-bg-dark';
        }

        return $badge;
    }
}
