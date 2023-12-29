<?php

declare(strict_types=1);

namespace App\Enums;

enum LeadStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
}
