<?php

namespace App\Models\Job;

use App\Models\Client\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function client_details() {
        return $this->hasOne(Client::class, 'id', 'client');
    }

    public function job_worker_details() {
        return $this->hasMany(JobWorker::class, 'job_id', 'id')->with('worker_details');
    }
}
