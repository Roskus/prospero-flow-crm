<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Country Model
 */
class Country extends Model
{
    protected $table = 'country';

    public function getAll()
    {
        return Country::all();
    }
}
