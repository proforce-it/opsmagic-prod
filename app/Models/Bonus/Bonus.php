<?php

namespace App\Models\Bonus;

use App\Models\Client\ClientJob;
use App\Models\Worker\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bonus extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded;

    public function worker_details() {
        return $this->hasOne(Worker::class, 'id', 'worker_id')->select('id', 'worker_no', 'first_name', 'middle_name', 'last_name', 'payroll_reference');
    }

    public function job_details() {
        return $this->hasOne(ClientJob::class, 'id', 'job_id')->with(['client_details', 'site_details']);
    }
}
