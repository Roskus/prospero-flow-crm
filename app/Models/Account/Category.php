<?php

declare(strict_types=1);

namespace App\Models\Account;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'AccountCategory', required: ['name'], type: 'object')]
class Category extends Model
{
    use HasFactory;

    protected $table = 'account_category';

    protected $fillable = [
        'company_id',
        'name',
    ];

    #[OAT\Property(property: 'id', type: 'integer', example: 1)]
    #[OAT\Property(property: 'name', type: 'string', example: 'Sales')]
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'account_category_id');
    }
}
