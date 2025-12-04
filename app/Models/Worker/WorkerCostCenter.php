<?php

namespace App\Models\Worker;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerCostCenter extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function worker() {
        return $this->belongsTo(Worker::class);
    }
}
