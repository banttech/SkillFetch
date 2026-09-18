<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorExperience extends Model
{
    protected $table = 'supervisor_experiences';

    protected $fillable = [
        'supervisor_id',
        'experience_id'
    ];
}
