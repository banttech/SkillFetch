<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Answer;
use App\Models\Department;

class Question extends Model
{
    protected $fillable = [
        'question',
        'department_id',
        'answer_type',      // text, single, multi, image, video
        'sub_answer_type',  // only for image/video: text, single, multi
        'media_path',       // image or video file path
        'status',
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}