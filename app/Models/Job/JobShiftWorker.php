<?php

namespace App\Models\Job;

use App\Models\User;
use App\Models\Worker\RightsToWork;
use App\Models\Worker\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobShiftWorker extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function worker() {
        return $this->belongsTo(Worker::class)->select(['id', 'worker_no', 'first_name', 'middle_name', 'last_name', 'payroll_reference', 'email_address', 'mobile_number', 'date_of_birth']);
    }

    public function rightsToWork() {
        return $this->hasMany(RightsToWork::class, 'worker_id', 'worker_id');
    }

    public function jobShift() {
        return $this->belongsTo(JobShift::class, 'job_shift_id')->with('client_job_details'); /*,'client_details'*/
    }

    public function job_line_details() {
        return $this->hasOne(JobLine::class, 'id', 'job_line_id');
    }

    public function worker_all_details() {
        return $this->hasOne(Worker::class, 'id', 'worker_id')
            ->with(['worker_cost_centres_with_name', 'worker_cost_center', 'latest_end_date_rights_to_work_details', 'id_documents', 'nationality_details', 'worker_payroll_references', 'accommodation_details']);
    }
}
