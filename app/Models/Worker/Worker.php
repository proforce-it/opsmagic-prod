<?php

namespace App\Models\Worker;

use App\Models\Accommodation\Accommodation;
use App\Models\Activity\ActivityLog;
use App\Models\Client\ClientJobWorker;
use App\Models\Group\Group;
use App\Models\Job\JobShiftWorker;
use App\Models\Location\Country;
use App\Models\Note\Note;
use App\Models\Payroll\WorkerPayrollReference;
use App\Models\PickUpPoint\PickUpPoint;
use App\Models\Timesheet\Timesheet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Worker extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;
    protected $guarded = [];

    public function note_details() {
        return $this->hasMany(Note::class, 'worker_id', 'id')->with('user_details')->where('type', 'Worker')->orderBy('id', 'desc');
    }

    public function worker_cost_center() {
        return $this->hasMany(WorkerCostCenter::class, 'worker_id', 'id');
    }

    public function rights_to_work_details() {
        return $this->hasMany(RightsToWork::class, 'worker_id', 'id')->orderBy('id', 'desc');
    }

    public function incomplete_rights_to_work_details() {
        return $this->hasOne(RightsToWork::class, 'worker_id', 'id')->where('incomplete', 1)->orderBy('id', 'desc');
    }

    public function latest_end_date_rights_to_work_details() {
        return $this->hasOne(RightsToWork::class, 'worker_id', 'id')->latestOfMany('end_date'); //->orderBy('end_date', 'desc');
    }

    public function worker_documents() {
        return $this->hasMany(WorkerDocument::class, 'worker_id', 'id')->orderBy('id', 'desc')->with('uploaded_by_details');
    }

    public function id_documents() {
        return $this->hasMany(WorkerDocument::class, 'worker_id', 'id')->orderBy('id', 'desc')
            ->where('document_file_title', 'ID')->with('uploaded_by_details');
    }

    /*public function registration_documents() {
        return $this->hasMany(WorkerDocument::class, 'worker_id', 'id')->orderBy('id', 'desc')
            ->where('type', 'registration')->with('uploaded_by_details');
    }

    public function other_documents() {
        return $this->hasMany(WorkerDocument::class, 'worker_id', 'id')->orderBy('id', 'desc')
            ->where('type', 'other')->with('uploaded_by_details');
    }*/

    public function clientJobWorkers() {
        return $this->hasMany(ClientJobWorker::class, 'worker_id', 'id')->with('job');
    }

    public function leaverLog() {
        return $this->hasOne(ActivityLog::class, 'log_for_id', 'id')->where('field', 'status')->where('new_value', 'Leaver')->orderBy('id', 'desc');
    }

    public function last_working_log() {
        //return $this->hasOne(JobShiftWorker::class, 'worker_id', 'id')->whereNotNull('confirmed_at')->orderBy('shift_date', 'desc')->with('jobShift');
        return $this->hasOne(Timesheet::class, 'worker_id', 'id')->orderBy('date', 'desc')->with('jobShift');
    }

    public function jobShiftWorker() {
        return $this->hasMany(JobShiftWorker::class, 'worker_id', 'id');
    }

    public function timesheet_detail() {
        return $this->hasMany(Timesheet::class, 'worker_id', 'id');
    }

    public function rights_to_work_latest_date() {
        return $this->hasOne(RightsToWork::class, 'worker_id', 'id')->orderBy('end_date', 'desc');
    }

    public function accommodation_details() {
        return $this->hasOne(Accommodation::class, 'id', 'accommodation_site');
    }

    public function preferred_pickup_point() {
        return $this->belongsTo(PickUpPoint::class, 'preferred_pick_up_point_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_with_workers', 'worker_id', 'group_id')
            ->withTimestamps()
            ->withPivot('deleted_at'); // Include 'deleted_at' if using soft deletes
    }

    public function worker_cost_centres_with_name() {
        return $this->hasMany(WorkerCostCenter::class, 'worker_id', 'id')
            ->join('cost_centres', 'worker_cost_centers.cost_center', '=', 'cost_centres.id')
            ->select('worker_cost_centers.worker_id', 'cost_centres.id as cost_center_id', 'cost_centres.short_code as cost_center_short_code');
    }

    public function nationality_details() {
        return $this->hasOne(Country::class, 'id', 'nationality');
    }

    public function worker_payroll_references() {
        return $this->hasOne(WorkerPayrollReference::class, 'worker_id', 'id')
            ->whereNull('expires_on')
            ->orderByDesc('id');
    }
}
