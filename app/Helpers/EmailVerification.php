<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Values of the email_verified column of leads, customers, contacts and supplier contacts.
 */
class EmailVerification
{
    public const PENDING = 0;

    public const VALID = 1;

    public const INVALID = 3;
}
