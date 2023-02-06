<?php

declare(strict_types=1);

namespace App\Models;

use App\Mail\GenericEmail;
use App\Models\Ticket\Message;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Ticket', required: ['title', 'description'], type: 'object')]
class Ticket extends Model
{
    use HasFactory;

    protected $table = 'ticket';

    protected $fillable = [
        'title',
        'description',
        'customer_id',
        'type',
        'priority',
        'status',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    #[OAT\Property(property: 'id', type: 'int', example: 1)]
    #[OAT\Property(property: 'title', type: 'string', example: 'An issue occur with the order #3')]
    #[OAT\Property(property: 'description', type: 'string', example: 'My order dosen\'t include one of the products we request')]
    #[OAT\Property(property: 'customer_id', type: 'int', example: 1)]
    #[OAT\Property(property: 'type', type: 'string', example: 'issue')]
    #[OAT\Property(property: 'priority', type: 'string', example: 'medium')]
    #[OAT\Property(property: 'order_id', type: 'int', example: 1)]
    #[OAT\Property(property: 'status', type: 'string', example: 'new')]
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function assignedTo(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'assigned_to');
    }

    public function getLatestByCompany(int $company_id)
    {
        return Ticket::with('customer', 'createdBy')->where('company_id', $company_id)->orderBy('created_at', 'DESC')->get();
    }

    public function priorities(): array
    {
        return ['low', 'medium', 'high'];
    }

    public function types(): array
    {
        return ['issue', 'product'];
    }

    public function statuses(): array
    {
        return ['new', 'assigned', 'duplicated', 'closed'];
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getAllByCompanyId(int $company_id, ?string $search)
    {
        $tickets = Ticket::where('company_id', $company_id);

        if (! empty($search)) {
            $tickets
                ->where('title', 'LIKE', "%$search%")
                ->orWhere('description', 'LIKE', "%$search%")
                ->orWhere('status', 'LIKE', "%$search%");
        }

        return $tickets->orderBy('created_at', 'desc')->paginate(10);
    }

    protected static function booted(): void
    {
        static::updated(function ($ticket): void {
            if ($ticket->wasChanged('status')) {
                if ($ticket->status == 'closed') {
                    $subject = __('The ticket #:ticket_number has been closed.', ['ticket_number' => (string) $ticket->id]);
                    $body = __('The ticket #:ticket_number has been closed.', ['ticket_number' => (string) $ticket->id]);
                } else {
                    $subject = __('Ticket #:ticket_number status has changed.', ['ticket_number' => $ticket->id]);
                    $data = [
                        'ticket_number' => $ticket->id,
                        'original_status' => Str::upper($ticket->getOriginal('status')),
                        'current_status' => Str::upper($ticket->status),
                    ];
                    $body = __('Ticket #:ticket_number status has changed from :original_status to :current_status.', $data);
                }

                try {
                    Mail::to($ticket->customer->email)->send(new GenericEmail($ticket->company, $subject, ['body' => $body]));
                } catch (\Throwable $throwable) {
                    Log::error($throwable->getMessage());
                }
            }
        });
    }
}
