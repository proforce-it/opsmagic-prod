<?php

namespace App\Models\Job;

use App\Models\Worker\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobWorker extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function worker_details() {
        return $this->hasOne(Worker::class, 'id', 'worker_id');
    }
}
