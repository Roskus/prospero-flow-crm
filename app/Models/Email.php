<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Email\Attach;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function getAll()
    {
        return Email::orderBy('created_at', 'DESC')->get();
    }

    public function getAllByCompanyId(int $company_id, ?string $search = null, ?array $filters = null)
    {
        if (empty($search)) {
            $email = Email::where('company_id', $company_id);
        } else {
            $email = Email::where('to', 'LIKE', "%$search%")
                ->orWhere('subject', 'LIKE', "%$search%");
        }

        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $email->where($key, $filter);
            }
        }

        return $email->orderByDesc('id')->paginate(10);
    }
}
