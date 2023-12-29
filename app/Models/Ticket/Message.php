<?php

declare(strict_types=1);

namespace App\Models\Ticket;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'ticket_message';

    protected $fillable = [
        'ticket_id',
        'body',
        'author_id',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
