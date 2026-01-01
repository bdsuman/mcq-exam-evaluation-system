<?php

namespace App\Models;

use App\Builders\TaskBuilder;
use App\Traits\TranslatAble;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory,TranslatAble;

    protected $fillable = [
        'user_id',
        'is_completed',
        'avatar',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public array $translatable = ['title', 'description'];


    /**
     * Get the avatar URL. Appended only when specifically requested.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (is_null($this->avatar)) {
            return null;
        }

        // Return external URLs as-is
        if (str_contains($this->avatar, 'http')) {
            return $this->avatar;
        }

        // Get storage disk with fallback
        $disk = config('filesystems.default') === 'exoscale' ? 'exoscale' : config('filesystems.default');

        return Storage::disk($disk)->url($this->avatar);
    }

    /**
     * Build a custom Eloquent query builder.
     */
    public function newEloquentBuilder($query): TaskBuilder
    {
        return new TaskBuilder($query);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
