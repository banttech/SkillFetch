<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'supervisor_id',
        'skill_id'
    ];

    public function attempt()
    {
        return $this->belongsTo(SupervisorTestAttempt::class, 'attempt_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }
}
