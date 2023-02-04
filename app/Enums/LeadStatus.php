<?php

declare(strict_types=1);

namespace App\Enums;

enum LeadStatus: string
{
    case Open = 'open';
    case FirstContact = 'first_contact';
    case Recall = 'recall';
    case Quote = 'quote';
    case Quoted = 'quoted';
    case WaitingForAnswer = 'waiting_for_answer';
    case Standby = 'standby';
    case Closed = 'closed';
    case InProgress = 'in_progress';
    case Converted = 'converted'; // Promoted to customer
}
