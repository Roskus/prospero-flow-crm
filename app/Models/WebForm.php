<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebForm extends Model
{
    use SoftDeletes;

    protected $table = 'web_form';

    protected $fillable = [
        'company_id',
        'name',
        'fields',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];
}
