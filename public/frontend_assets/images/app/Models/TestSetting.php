<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSetting extends Model
{
    protected $table = 'test_settings';

    protected $fillable = [
        'name',
        'fees',
        'marks',
        'timing',
        'question_per_department',
        'no_of_departments',
        'total_question',
        'total_marks',
        'passing_marks'
    ];

    public function supervisorPayments()
    {
        return $this->hasMany(SupervisorTestFees::class, 'test_id');
    }

}
