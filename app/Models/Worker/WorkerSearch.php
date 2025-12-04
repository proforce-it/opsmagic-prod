<?php

namespace App\Models\Worker;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkerSearch extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
}
