<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Ticket', required: ['title', 'description'], type: 'object')]
class Ticket extends Model
{
    use HasFactory;

    protected $table = 'ticket';

    protected $fillable = [
        'title',
        'description',
        'customer_id',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $id;

    #[OAT\Property(type: 'string', example: 'An issue occur with the order #3')]
    protected string $title;

    #[OAT\Property(type: 'string', example: 'My order dosen\'t include one of the products we request')]
    protected string $description;

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $customer_id;

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function createdBy()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'created_by');
    }

    public function getLatestByCompany(int $company_id)
    {
        return Ticket::with('customer', 'createdBy')->where('company_id', $company_id)->orderBy('created_at', 'DESC')->get();
    }

    public function priorities(): array
    {
        return ['low', 'medium', 'high'];
    }
}
