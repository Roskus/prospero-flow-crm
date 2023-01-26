<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Bank', required: ['name'])]
class Bank extends Model
{
    const ACTIVE = 1;

    protected $table = 'bank';

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $id;

    #[OAT\Property(type: 'string', example: 'Bank of America')]
    protected string $name;


    public function getAll()
    {
        return Bank::orderBy('name', 'asc')->get();
    }
}
