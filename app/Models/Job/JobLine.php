<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobLine extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded;

    public function job_line_client_requirements_details() {
        return $this->hasMany(JobLineClientRequirement::class, 'job_line_id', 'id');
    }
}
