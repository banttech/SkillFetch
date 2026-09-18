<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Supervisor;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\WorkLocation;
use App\Models\PostJob;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'image',
        'role_id',
        'status',
        'verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

   
    public function supervisorDetail()
    {
        return $this->hasOne(Supervisor::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'supervisor_skills');
    }

    public function experiences()
    {
        return $this->belongsToMany(Experience::class, 'supervisor_experiences');
    }

    public function workLocations()
    {
        return $this->belongsToMany(WorkLocation::class, 'supervisor_work_locations');
    }

    public function Jobs()
    {
        return $this->hasMany(PostJob::class, 'employer_id');
    }

    public function employerDetail()
    {
        return $this->hasOne(EmployerDetail::class, 'user_id');
    }





}
