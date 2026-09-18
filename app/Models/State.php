<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';

    protected $fillable = [
        'name',
    ];

    // A state has many supervisors
    public function supervisors()
    {
        return $this->hasMany(Supervisor::class, 'state_id', 'id');
    }
}
