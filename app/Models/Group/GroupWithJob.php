<?php

namespace App\Models\Group;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupWithJob extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'group_id',
        'job_id',
        'deleted_at',
    ];

    public function groups() {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }
}
