<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'notification';

    public function getLatestByUser(int $user_id)
    {
        return Notification::where('user_id', $user_id)->orderBy('created_at', 'DESC')->limit(10)->get();
    }
}
