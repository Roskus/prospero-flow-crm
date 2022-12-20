<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'ticket';

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
