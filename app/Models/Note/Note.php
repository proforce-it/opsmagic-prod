<?php

namespace App\Models\Note;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user_details() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
