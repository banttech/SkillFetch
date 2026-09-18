<?php

namespace App\Models;
use App\Models\PostJob;
use App\Models\Experience;
use App\Models\EmployerDetail;

use Illuminate\Database\Eloquent\Model;

class EmployerJobExperience extends Model
{
    protected $table = 'employer_job_experience';

    protected $fillable = [
        'job_id',
        'experience_id',
        'employer_id'
    ];

    public function job()
    {
        return $this->belongsTo(PostJob::class, 'job_id');
    }

    public function experience()
    {
        return $this->belongsTo(Experience::class, 'experience_id');
    }

    public function employer()
    {
        return $this->belongsTo(EmployerDetail::class, 'employer_id');
    }
}
