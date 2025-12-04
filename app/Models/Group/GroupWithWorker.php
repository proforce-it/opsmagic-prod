<?php

namespace App\Models\Group;

use App\Models\Worker\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupWithWorker extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'group_with_workers';

    protected $primaryKey = 'id';

    protected $fillable = ['group_id', 'worker_id', 'deleted_at'];


    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function worker(){
        return $this->belongsTo(Worker::class);
    }
}
