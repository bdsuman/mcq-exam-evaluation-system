<?php

namespace App\Models;

use App\Builders\TaskArchivesBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TaskArchives extends Model
{
    protected $fillable = [
        'original_task_id',
        'user_id',
        'is_completed',
        'avatar',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

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
    public function newEloquentBuilder($query): TaskArchivesBuilder
    {
        return new TaskArchivesBuilder($query);
    }
}
