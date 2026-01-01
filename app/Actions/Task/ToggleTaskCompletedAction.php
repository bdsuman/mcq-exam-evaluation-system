<?php

namespace App\Actions\Task;

use App\Models\Task;

class ToggleTaskCompletedAction
{
    public function execute(Task $task): Task
    {
        $task->is_completed = !$task->is_completed;
        $task->save();

        return $task->fresh();
    }
}
