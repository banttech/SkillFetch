<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PostJob;
use App\Models\Skill;
use App\Models\EmployerDetail;

class EmployerJobSkill extends Model
{
    protected $table = 'employer_job_skills';

    protected $fillable = [
        'job_id',
        'skill_id',
        'employer_id'
    ];

    public function job()
    {
        return $this->belongsTo(PostJob::class, 'job_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }

    public function employer()
    {
        return $this->belongsTo(EmployerDetail::class, 'employer_id');
    }
}
