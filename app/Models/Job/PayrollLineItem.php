<?php

namespace App\Models\Job;

use App\Models\Client\Client;
use App\Models\Client\ClientJob;
use App\Models\Client\Site;
use App\Models\Worker\Worker;
use App\Models\Worker\WorkerCostCenter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollLineItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function worker_details() {
        return $this->hasOne(Worker::class, 'id', 'worker_id')
            ->select('id', 'worker_no', 'first_name', 'middle_name', 'last_name', 'payroll_reference');
    }

    public function job_details() {
        return $this->hasOne(ClientJob::class, 'id', 'job_id')
            ->with(['client_details', 'site_details']);
    }

    public function worker_cost_center() {
        return $this->hasMany(WorkerCostCenter::class, 'worker_id', 'worker_id');
    }

    public function site_details() {
        return $this->hasOne(Site::class, 'id', 'site_id');
    }
}
