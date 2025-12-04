<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientContact extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function client_details() {
        return $this->hasOne(Client::class, 'id', 'client_id')->select(['id', 'company_name', 'bonus_commission_percentage', 'payroll_week_starts', 'company_logo'])->withTrashed();
    }
}
