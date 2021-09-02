<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    const ACTIVE = 1;

    protected $table = 'account';

    public function getAll()
    {
        return Account::orderBy('created_at', 'desc')->get();
    }
}
