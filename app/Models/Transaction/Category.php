<?php

declare(strict_types=1);

namespace App\Models\Transaction;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'TransactionCategory', required: ['name'], type: 'object')]
class Category extends Model
{
    use HasFactory;

    protected $table = 'transaction_category';

    protected $fillable = [
        'company_id',
        'name',
    ];

    #[OAT\Property(property: 'id', type: 'integer', example: 1)]
    #[OAT\Property(property: 'name', type: 'string', example: 'Sales')]
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'transaction_category_id');
    }
}
