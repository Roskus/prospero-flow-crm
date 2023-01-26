<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Industry', required: ['name'])]
class Industry extends Model
{
    use HasFactory;

    protected $table = 'industry';

    #[OAT\Property(type: 'int', example: 6)]
    protected ?int $id;

    #[OAT\Property(type: 'string', example: 'Automotive')]
    protected string $name;

    public function getAll()
    {
        //@TODO Add to the cache
        return Industry::all();
    }
}
