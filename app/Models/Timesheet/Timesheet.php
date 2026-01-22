<?php

namespace App\Models\Timesheet;

use App\Models\Client\ClientJob;
use App\Models\Job\JobShift;
use App\Models\Worker\Worker;
use App\Models\Worker\WorkerCostCenter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timesheet extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function job_details() {
        return $this->hasOne(ClientJob::class, 'id', 'job_id')->with(['client_details', 'site_details']);
    }

    public function job_details_grouping_id() {
        return $this->hasOne(ClientJob::class, 'id', 'job_id')->groupBy('id');
    }

    public function worker_details() {
        return $this->hasOne(Worker::class, 'id', 'worker_id')
            ->select('id', 'worker_no', 'first_name', 'middle_name', 'last_name', 'payroll_reference', 'status')
            ->with('worker_cost_center');
    }

    public function shift_details() {
        return $this->hasMany(JobShift::class, 'job_id', 'job_id');
    }

    public function jobShift() {
        return $this->belongsTo(JobShift::class, 'job_id', 'job_id')->with(['client_job_details', 'client_details']);
    }
}
