<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function client_documents() {
        return $this->hasMany(ClientDocument::class, 'client_id', 'id')->orderBy('id', 'desc')->with('uploaded_by_details');
    }

    public function client_job_details() {
        return $this->hasMany(ClientJob::class, 'client_id', 'id')->with('job_shift_details');
    }

    public function client_site_details() {
        return $this->hasMany(Site::class, 'client_id', 'id');
    }

    public function client_site_details_with_job() {
        return $this->hasMany(Site::class, 'client_id', 'id')->with('job_details');
    }
}
