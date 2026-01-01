<?php

namespace App\Models;

use App\Traits\TranslatAble;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory, TranslatAble;

    protected $fillable = [
        'type',
        'question',
        'mark',
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
        'mark' => 'integer',
    ];

    public array $translatable = ['question'];

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }
}
