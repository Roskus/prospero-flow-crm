<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Module extends Model
{
    protected $table = 'module';

    public const int LEAD = 1;

    public const int CUSTOMER = 2;

    public const int PRODUCT = 3;

    public const int ORDER = 4;

    public const int SUPPLIER = 5;

    public const int ACCOUNTING = 6;

    public const int USER = 7;

    public const int COMPANY = 8;

    public const int REPORT = 9;

    public const int TICKET = 10;

    public const int HUMAN_RESOURCES = 14;
}
