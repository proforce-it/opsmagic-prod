<?php

namespace App\Models\Worker;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkerPendingRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function worker() {
        return $this->belongsTo(Worker::class, 'worker_id')
            ->select('id', 'first_name', 'middle_name', 'last_name', 'current_address_line_one', 'current_address_line_two', 'current_city', 'current_state', 'current_post_code', 'current_country', 'mobile_number');
    }
}
