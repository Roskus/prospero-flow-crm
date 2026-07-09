<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Payroll',
    required: ['user_id', 'gross_amount', 'net_amount', 'period_year', 'period_month'],
    properties: [
        new OAT\Property(property: 'id', description: 'Payroll ID', type: 'integer', example: 1),
        new OAT\Property(property: 'user_id', description: 'User ID', type: 'integer', example: 1),
        new OAT\Property(property: 'gross_amount', description: 'Gross payroll amount', type: 'number', format: 'float', example: 5000.00),
        new OAT\Property(property: 'net_amount', description: 'Net payroll amount', type: 'number', format: 'float', example: 3800.00),
        new OAT\Property(property: 'period_year', description: 'Payroll period year', type: 'integer', example: 2026),
        new OAT\Property(property: 'period_month', description: 'Payroll period month', type: 'integer', example: 7),
        new OAT\Property(property: 'payment_date', description: 'Payment date', type: 'string', format: 'date', example: '2026-07-07'),
        new OAT\Property(property: 'iban', description: 'IBAN for payment', type: 'string', example: 'ES9121000418450200051332'),
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

    protected $fillable = ['user_id', 'gross_amount', 'net_amount', 'payment_date', 'period_year', 'period_month', 'iban', 'file', 'notes'];

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
