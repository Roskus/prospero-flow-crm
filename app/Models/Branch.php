<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasAddress;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Branch',
    required: ['name', 'code'],
    properties: [
        new OAT\Property(property: 'id', description: 'Branch ID', type: 'integer', example: 1),
        new OAT\Property(property: 'name', description: 'Name of the branch', type: 'string', example: 'Centro'),
        new OAT\Property(property: 'code', description: 'Code of the branch, unique in the company', type: 'string', example: '0002'),
        new OAT\Property(property: 'is_main', description: 'Whether it is the main branch of the company', type: 'boolean', example: false),
        new OAT\Property(property: 'phone', description: 'Phone of the branch', type: 'string', example: '+34910000000'),
        new OAT\Property(property: 'email', description: 'Email of the branch', type: 'string', format: 'email', example: 'centro@company.com'),
    ],
    type: 'object',
    allOf: [new OAT\Schema(ref: '#/components/schemas/Address')],
)]
class Branch extends Model
{
    use HasAddress;
    use HasFactory;
    use SoftDeletes;

    const string MAIN_CODE = '0001';

    protected $table = 'branch';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'is_main',
        'country_id',
        'province',
        'city',
        'locality',
        'street',
        'address_extra',
        'zipcode',
        'latitude',
        'longitude',
        'phone',
        'email',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * The code of a branch with invoices is their series, so it can no longer change.
     */
    public function hasInvoices(): bool
    {
        return $this->exists && $this->invoices()->exists();
    }

    #[Scope]
    protected function ofCompany(Builder $query, int $companyId): void
    {
        $query->where('company_id', $companyId);
    }
}
