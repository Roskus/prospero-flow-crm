<?php

declare(strict_types=1);

namespace App\Models\Email;

use App\Models\Email;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attach extends Model
{
    protected $table = 'email_attach';

    protected $guarded = [];

    public function email(): BelongsTo
    {
        return $this->belongsTo(Email::class);
    }
}
