<?php

namespace App\Models\Worker;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerDocument extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function uploaded_by_details() {
        return $this->hasOne(User::class, 'id', 'uploaded_by');
    }
}
