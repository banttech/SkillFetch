<?php

namespace App\Models;

use App\Models\User;
use App\Models\Job;
use App\Models\EmployerJobSkill;
use App\Models\EmployerJobExperience;


use Illuminate\Database\Eloquent\Model;


class EmployerDetail extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'company_name',
        'address',
        'city',
        'state',
        'pin_code',
        'identity_proof_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jobs()
    {
        return $this->hasMany(PostJob::class, 'employer_id');
    }

    public function jobSkills()
    {
        return $this->hasMany(EmployerJobSkill::class, 'employer_id');
    }

    public function jobExperiences()
    {
        return $this->hasMany(EmployerJobExperience::class, 'employer_id');
    }
}
