<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class TaskObserver
{
    /**
     * Clear user's task cache
     */
    private function clearUserTaskCache(Task $task): void
    {
        $userId = $task->user_id;
        
        // Clear all task cache patterns for this user
        $patterns = [
            "tasks_user_{$userId}_*",
        ];

        foreach ($patterns as $pattern) {
            // For file/database cache drivers, we need to clear manually
            // Get all possible cache keys (pages 1-100, per_page variations)
            for ($page = 1; $page <= 100; $page++) {
                foreach ([10, 15, 20, 25, 50, 100] as $perPage) {
                    $cacheKey = "tasks_user_{$userId}_page_{$page}_per_{$perPage}";
                    Cache::forget($cacheKey);
                }
            }
        }
    }

    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        $this->clearUserTaskCache($task);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        $this->clearUserTaskCache($task);
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        $this->clearUserTaskCache($task);
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        $this->clearUserTaskCache($task);
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        $this->clearUserTaskCache($task);
    }
}
