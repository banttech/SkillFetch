<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorSkill extends Model
{
    protected $table = 'supervisor_skills';

    protected $fillable = [
        'supervisor_id',
        'skill_id'
    ];
}
