<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $table = 'payroll';

    protected $fillable = ['user_id', 'amount', 'payment_date', 'file', 'notes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAllByYear(int $year)
    {
        return Payroll::whereYear('payment_date', $year)->get();
    }
}
