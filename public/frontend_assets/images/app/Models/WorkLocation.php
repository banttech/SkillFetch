<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkLocation extends Model
{
    protected $table = 'work_locations';

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'supervisor_work_locations');
    }

    public function postJobs()
    {
        return $this->belongsToMany(
            PostJob::class,
            'post_job_locations',
            'work_location_id',
            'post_job_id'
        );
    }
}
