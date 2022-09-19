<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;
    protected $table = 'email';

    const DRAFT = 'draft';
    const QUEUE = 'queue';
    const SENT = 'sent';
    const ERROR = 'error';

    public function getAll()
    {
        return Email::orderBy('created_at', 'DESC')->get();
    }
}
