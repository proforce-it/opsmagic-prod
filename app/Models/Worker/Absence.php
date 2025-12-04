<?php

namespace App\Models\Worker;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Absence extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function added_by_details() {
        return $this->hasOne(User::class, 'id', 'added_by');
    }
}
