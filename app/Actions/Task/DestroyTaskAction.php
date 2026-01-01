<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Models\TaskArchives;
use App\Models\Translation;

class DestroyTaskAction
{
    public function execute(Task $task): void
    {
        $taskArchives = new TaskArchives();
        $taskArchives->user_id = $task->user_id;
        $taskArchives->original_task_id = $task->id;
        $taskArchives->avatar = $task->avatar;
        $taskArchives->is_completed = $task->is_completed;
        $taskArchives->save();

        Translation::where('translatable_type', Task::class)
            ->where('translatable_id', $task->id)
            ->each(function ($translation) use ($taskArchives) {
                $translation->translatable_type = TaskArchives::class;
                $translation->translatable_id = $taskArchives->id;
                $translation->save();
            });

        $task->delete();
    }
}
