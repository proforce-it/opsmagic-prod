<?php

namespace App\Models\Client;

use App\Models\Job\PayrollLineItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function client_details() {
        return $this->hasOne(Client::class, 'id', 'client_id')->select(['id', 'company_name', 'bonus_commission_percentage', 'payroll_week_starts', 'company_logo'])->withTrashed();
    }

    public function job_details() {
        return $this->hasMany(ClientJob::class, 'site_id', 'id')->with(['pay_rate_details', 'job_shift_details']);
    }

    public function payroll_line_item_details() {
        return $this->hasMany(PayrollLineItem::class, 'site_id', 'id');
    }
}
