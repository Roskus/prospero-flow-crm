<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Payroll',
    required: ['user_id', 'amount', 'payment_date'],
    properties: [
        new OAT\Property(property: 'id', description: 'Payroll ID', type: 'integer', example: 1),
        new OAT\Property(property: 'user_id', description: 'User ID', type: 'integer', example: 1),
        new OAT\Property(property: 'amount', description: 'Payroll amount', type: 'number', format: 'float', example: 5000.00),
        new OAT\Property(property: 'payment_date', description: 'Payment date', type: 'string', format: 'date', example: '2026-07-07'),
        new OAT\Property(property: 'file', description: 'Payroll file attachment', type: 'string', example: 'payroll_2026_07.pdf'),
        new OAT\Property(property: 'notes', description: 'Additional notes', type: 'string', example: 'July payroll'),
    ],
    type: 'object'
)]
class Payroll extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'payroll';

    protected $fillable = ['user_id', 'amount', 'payment_date', 'file', 'notes'];

    protected $hidden = [
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAllByYear(int $year)
    {
        return Payroll::whereYear('payment_date', $year)->get();
    }
}
