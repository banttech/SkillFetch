<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    use HasFactory;

    protected $table = 'job_titles';

    protected $fillable = [
        'title',
        'created_by',
        'user_id',
    ];

    public function postJobs()
    {
        return $this->hasMany(PostJob::class, 'job_title_id');
    }
}
