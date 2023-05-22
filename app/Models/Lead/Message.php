<?php

declare(strict_types=1);

namespace App\Models\Lead;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'lead_message';

    protected $fillable = [
        'lead_id',
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
