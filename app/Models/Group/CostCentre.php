<?php

namespace App\Models\Group;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostCentre extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cost_centres';

    protected $fillable = [
        'name',
        'short_code',
    ];

    // Relationships
    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
