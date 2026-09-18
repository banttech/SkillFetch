<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostJob extends Model
{
    use HasFactory;



    protected $fillable = [
        'employer_id',
        'job_title_id',
        'title',
        'description',
        'years',
        'status',
        'paymentStatus',
        'payment_order_id',
        'transaction_id',
        'paidAt',
        'fixed_salary',
        'variable_salary_from',
        'variable_salary_to',
        'petrol_allowance',
        'accommodation',
        'food_allowance'
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

    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'post_job_skills',
            'post_job_id',
            'skill_id'
        );
    }

    public function experiences()
    {
        return $this->belongsToMany(
            Experience::class,
            'post_job_experiences',
            'post_job_id',
            'experience_id'
        );
    }

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id');
    }

    public function employerSkills()
    {
        return $this->belongsToMany(
            EmployerSkill::class,
            'post_job_employer_skills',
            'post_job_id',
            'employer_skill_id'
        );
    }

    public function workLocations()
    {
        return $this->belongsToMany(
            WorkLocation::class,
            'post_job_locations',
            'post_job_id',
            'work_location_id'
        );
    }

    // Supervisors who applied for this job
    public function appliedSupervisors()
    {
        return $this->belongsToMany(
            Supervisor::class,
            'supervisor_applied_jobs',
            'job_id',
            'supervisor_id'
        )->withTimestamps()->withPivot('status', 'applied_at')->with([
        'user',
        'workLocations',   // ✅ load work locations
        'skills',
        'experiences'
    ]);
    }
}
