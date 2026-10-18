<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Email\Attach;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use OpenApi\Attributes as OAT;
use Yajra\Auditable\AuditableTrait;

#[OAT\Schema(
    schema: 'Email',
    required: ['from', 'to', 'subject', 'body'],
    properties: [
        new OAT\Property(property: 'id', description: 'Email ID', type: 'integer', example: 1),
        new OAT\Property(property: 'from', description: 'From email address', type: 'string', format: 'email', example: 'hello@company.com'),
        new OAT\Property(property: 'to', description: 'To email address', type: 'string', format: 'email', example: 'mail@company.com'),
        new OAT\Property(property: 'subject', description: 'Email subject', type: 'string', example: 'Email subject'),
        new OAT\Property(property: 'body', description: 'Email body content', type: 'string', example: 'Email body'),
        new OAT\Property(property: 'signature', description: 'Include signature in the email', type: 'boolean', example: true),
    ],
    type: 'object'
)]
class Email extends Model
{
    use AuditableTrait;
    use HasFactory;

    protected $guarded = [];

    protected $table = 'email';

    const string DRAFT = 'draft';

    const string QUEUE = 'queue';

    const string SENT = 'sent';

    const string ERROR = 'error';

    public function attachments(): HasMany
    {
        return $this->hasMany(Attach::class);
    }

    public function casts(): array
    {
        return [
            'opened_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Email $email) {
            if (empty($email->uuid)) {
                $email->uuid = (string) Str::uuid();
            }
        });
    }

    public function getAll()
    {
        return Email::orderBy('created_at', 'DESC')->get();
    }

    public function searchByCompany(int $company_id, ?string $search = null): Builder
    {
        return Email::where('company_id', $company_id)
            ->when(! empty($search), fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                $query->where('to', 'LIKE', "%$search%")
                    ->orWhere('subject', 'LIKE', "%$search%");
            }));
    }

    public function getAllByCompanyId(int $company_id, ?string $search = null, ?array $filters = null)
    {
        $email = $this->searchByCompany($company_id, $search);

        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $email->where($key, $filter);
            }
        }

        return $email->orderByDesc('id')->paginate(10);
    }
}
