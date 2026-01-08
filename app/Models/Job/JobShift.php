<?php

namespace App\Models\Job;

use App\Models\Client\Client;
use App\Models\Client\ClientJob;
use App\Models\Client\Site;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobShift extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function client_job_details() {
        return $this->belongsTo(ClientJob::class, 'job_id', 'id')->with(['site_details', 'client_details']);
    }

    public function client_details() {
        return $this->hasOneThrough(Client::class, ClientJob::class, 'id', 'id', 'job_id', 'client_id');
    }

    public function JobShiftWorker_details() {
        return $this->hasMany(JobShiftWorker::class, 'job_shift_id');
    }

    public function Job_line_client_requirement_details(){
        return $this->hasMany(JobLineClientRequirement::class,'job_shift_id','id');
    }
}
