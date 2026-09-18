<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorAppliedJob extends Model
{
    protected $table = 'supervisor_applied_jobs';

    protected $fillable = [
        'supervisor_id',
        'job_id',
        'status',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function job()
    {
        return $this->belongsTo(PostJob::class, 'job_id');
    }
}