<?php

namespace App\Models\Client;

use App\Models\Job\ClientJobPayRate;
use App\Models\Job\JobShift;
use App\Models\Job\PayrollLineItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientJob extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded;

    public function site_details() {
        return $this->hasOne(Site::class, 'id', 'site_id');
    }

    public function client_details() {
        //return $this->hasOne(Client::class, 'id', 'client_id');
        return $this->hasOne(Client::class, 'id', 'client_id')->select(['id', 'company_name', 'bonus_commission_percentage', 'payroll_week_starts', 'company_logo', 'deleted_at'])->withTrashed();
    }

    public function pay_rate_details() {
        return $this->hasOne(ClientJobPayRate::class, 'job_id', 'id')->where('status', 'C'); //C = Current
    }

    public function upcoming_pay_rate_details() {
        return $this->hasOne(ClientJobPayRate::class, 'job_id', 'id')->where('status', 'U'); //U = Upcoming
    }

    public function pay_rate_multiple() {
        return $this->hasMany(ClientJobPayRate::class, 'job_id', 'id')->orderBy('id', 'desc');
    }

    public function payroll_line_item_details() {
        return $this->hasMany(PayrollLineItem::class, 'job_id', 'id');
    }

    public function job_shift_details() {
        return $this->hasMany(JobShift::class, 'job_id', 'id')->with('JobShiftWorker_details');
    }

    public function client_job_worker_details() {
        return $this->hasMany(ClientJobWorker::class, 'job_id', 'id');
    }
}
