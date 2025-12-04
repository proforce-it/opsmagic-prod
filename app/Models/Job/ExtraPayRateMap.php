<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExtraPayRateMap extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function default_pay_rate_days_details() {
        return $this->hasMany(ExtraPayRateDay::class,'default_pay_rate_id', 'default_pay_rate_id');
    }
}
