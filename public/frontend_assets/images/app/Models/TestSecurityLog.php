<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSecurityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'supervisor_test_attempt_id',
        'violation_type',
        'violation_data',
        'ip_address',
        'user_agent',
    ];
}
