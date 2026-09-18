<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $table = 'experiences';

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'supervisor_experiences');
    }
   public function jobs()
    {
        return $this->belongsToMany(
            PostJob::class,
            'post_job_experiences',
            'experience_id',
            'post_job_id'
        );
    }

}
