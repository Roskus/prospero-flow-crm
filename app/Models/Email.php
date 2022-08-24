<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;
    protected $table = 'email';

    const DRAFT = 1;
    const QUEUE = 2;
    const SENT = 3;

    public function getAll()
    {
        return Email::orderBy('created_at', 'DESC')->get();
    }
}
