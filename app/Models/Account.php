<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Account', required: ['issue_date', 'name', 'amount'])]
class Account extends Model
{
    use SoftDeletes;

    const ACTIVE = 1;

    protected $table = 'account';

    protected $casts = [
        'issue_date' => 'datetime:Y-m-d',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $id;

    protected int $company_id;

    #[OAT\Property(type: 'string', example: 'Purchase computer')]
    protected string $name;

    #[OAT\Property(type: 'float', example: 999.75)]
    protected float $amount;

    public function getAll()
    {
        return Account::orderBy('created_at', 'desc')->get();
    }

    public function getAllActiveByCompany(int $company_id)
    {
        return Account::where('status', self::ACTIVE)
            ->where('company_id', $company_id)
            ->orderBy('created_at', 'desc')->get();
    }
}
