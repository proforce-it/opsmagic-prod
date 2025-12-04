<?php

namespace App\Models\Client;

use App\Models\PickUpPoint\PickUpPoint;
use App\Models\Worker\Absence;
use App\Models\Worker\RightsToWork;
use App\Models\Worker\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientJobWorker extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function rightsToWork()
    {
        return $this->hasMany(RightsToWork::class, 'worker_id', 'worker_id');
    }

    public function absence(){
        return $this->hasMany(Absence::class, 'worker_id', 'worker_id');
    }

    public function job_details() {
        return $this->hasOne(ClientJob::class, 'id', 'job_id');
    }

    public function job() {
        return $this->hasOne(ClientJob::class, 'id', 'job_id')->with(
            [
                'site_details',
                'client_details'
            ]
        );
    }
}
