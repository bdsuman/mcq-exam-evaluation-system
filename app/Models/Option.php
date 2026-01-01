<?php

namespace App\Models;

use App\Traits\TranslatAble;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model
{
    /** @use HasFactory<\Database\Factories\OptionFactory> */
    use HasFactory, TranslatAble;

    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public array $translatable = ['option_text'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
