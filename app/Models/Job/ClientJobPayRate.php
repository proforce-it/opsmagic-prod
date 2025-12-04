<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientJobPayRate extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function extra_pay_rate_map_details() {
        return $this->hasMany(ExtraPayRateMap::class, 'default_pay_rate_id', 'id');
    }

    public function default_pay_rate_days_details() {
        return $this->hasMany(ExtraPayRateDay::class,'default_pay_rate_id', 'id');
    }
}
