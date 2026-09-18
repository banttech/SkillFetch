<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorWorkLocation extends Model
{
    protected $table = 'supervisor_work_locations';

    protected $fillable = [
        'supervisor_id',
        'location_id'
    ];
}
