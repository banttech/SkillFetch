<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'years',
        'status',
    ];

    // PAYMENT
    public function payment()
    {
        return $this->hasOne(EmployerJobPayment::class, 'job_id');
    }

    // EMPLOYER INFO
    public function employer()
    {
        return $this->belongsTo(EmployerDetail::class, 'employer_id');
    }

    // JOB SKILLS
    public function skills()
    {
        return $this->hasMany(EmployerJobSkill::class, 'job_id');
    }

    // JOB EXPERIENCES
    public function experiences()
    {
        return $this->hasMany(EmployerJobExperience::class, 'job_id');
    }

    // SUPERVISORS WHO APPLIED FOR THIS JOB
    public function appliedJobs()
    {
        return $this->hasMany(SupervisorAppliedJob::class, 'job_id')
            ->with([
                'supervisor.user',
                'supervisor.workLocations',
                'supervisor.skills'
            ]);
    }

}
