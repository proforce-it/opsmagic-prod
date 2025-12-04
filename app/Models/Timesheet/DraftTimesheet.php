<?php

namespace App\Models\Timesheet;

use App\Models\Client\ClientJob;
use App\Models\Worker\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DraftTimesheet extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function worker_details() {
        return $this->hasOne(Worker::class, 'id', 'worker_id')
            ->select('id', 'worker_no', 'first_name', 'middle_name', 'last_name', 'payroll_reference', 'status');
    }

    public function job_details() {
        return $this->hasOne(ClientJob::class, 'id', 'job_id');
    }
}
