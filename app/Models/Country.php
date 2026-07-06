<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Country',
    required: ['name', 'iso_code'],
    properties: [
        new OAT\Property(property: 'id', description: 'Country ID', type: 'string', example: 'ES'),
        new OAT\Property(property: 'name', description: 'Country name', type: 'string', example: 'Spain'),
        new OAT\Property(property: 'iso_code', description: 'ISO 2-letter code', type: 'string', example: 'ES'),
    ],
    type: 'object'
)]
class Country extends Model
{
    protected $table = 'country';

    public function getAll()
    {
        return Country::all();
    }
}
