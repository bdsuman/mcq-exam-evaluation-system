<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_marks',
        'obtained_marks',
        'questions_answered',
        'correct_answers',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
