<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderNumber extends Model
{
    use HasFactory;

    protected $table = 'oder_number';

    protected $fillable = [
        'company_id',
        'last_order_number',
        'created_at',
        'updated_at',
    ];
}
