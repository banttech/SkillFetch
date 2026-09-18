<?php

namespace App\Models;

use App\Models\User;
use App\Models\State;
use App\Models\Skill;
use App\Models\Experience;

use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    protected $table = 'supervisors';

    protected $fillable = [
        'user_id',
        'address',
        'salary',
        'city',
        'state_id',
        'pincode',
        'own_bike',
        'own_phone',
        'aadhar_file',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'supervisor_skills',
            'supervisor_id',
            'skill_id'
        );
    }

    public function experiences()
    {
        return $this->belongsToMany(
            Experience::class,
            'supervisor_experiences',
            'supervisor_id',
            'experience_id'
        );
    }

    public function workLocations()
    {
        return $this->belongsToMany(
            WorkLocation::class,
            'supervisor_work_locations',
            'supervisor_id',
            'location_id'
        );
    }

    public function Payments()
    {
        return $this->hasMany(SupervisorTestFees::class);
    }

    public function testAttempts()
    {
        return $this->hasMany(SupervisorTestAttempt::class);
    }

    public function latestTestAttempt()
    {
        return $this->hasOne(SupervisorTestAttempt::class)
            ->orderBy('id', 'DESC'); // Use ID for consistency
    }


    public function passedTestAttempt()
    {
        return $this->hasOne(SupervisorTestAttempt::class)
            ->whereIn('test_submission_status', ['supervisor', 'automatic'])
            ->where('status', 'pass')
            ->latest('id');
    }

    // Relationship with applied jobs
    public function appliedJobs()
    {
        return $this->belongsToMany(
            PostJob::class,
            'supervisor_applied_jobs',
            'supervisor_id',
            'job_id'
        )->withTimestamps()->withPivot('status', 'applied_at');
    }


    public function profileViewPayments()
    {
        return $this->hasMany(SupervisorProfileViewPayment::class);
    }
}
