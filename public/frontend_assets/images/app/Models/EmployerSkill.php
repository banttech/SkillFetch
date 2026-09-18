<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'skills'
    ];

    public function postJobs()
    {
        return $this->belongsToMany(
            PostJob::class,
            'post_job_employer_skills',
            'employer_skill_id',
            'post_job_id'
        );
    }
}
