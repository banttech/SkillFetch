<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $table = 'skills';

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'supervisor_skills');
    }

    public function jobs()
    {
        return $this->belongsToMany(
            PostJob::class,
            'post_job_skills',
            'skill_id',
            'post_job_id'
        );
    }

}
