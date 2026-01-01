<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionSubmissionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'selected_option_ids',
        'correct_option_ids',
        'mark',
        'obtained_marks',
        'is_correct',
    ];

    protected $casts = [
        'selected_option_ids' => 'array',
        'correct_option_ids' => 'array',
        'is_correct' => 'boolean',
        'mark' => 'float',
        'obtained_marks' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
