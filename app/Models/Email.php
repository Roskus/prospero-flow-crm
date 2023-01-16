<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Email\Attach;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *    schema="Email",
 *    type="object"
 *  )
 */
class Email extends Model
{
    use HasFactory;

    protected $table = 'email';

    const DRAFT = 'draft';

    const QUEUE = 'queue';

    const SENT = 'sent';

    const ERROR = 'error';

    public function attachments()
    {
        return $this->hasMany(Attach::class);
    }

    public function getAll()
    {
        return Email::orderBy('created_at', 'DESC')->get();
    }

    /**
     * @param  int  $company_id
     * @param  string|null  $search
     * @param  array|null  $filters
     * @return mixed
     */
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

        return $email->paginate(10);
    }
}
