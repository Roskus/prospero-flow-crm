<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Values of the email_verified column of leads, customers, contacts and supplier contacts.
 */
enum EmailVerificationStatus: int
{
    case Pending = 0;
    case Valid = 1;
    case Invalid = 3;
}
