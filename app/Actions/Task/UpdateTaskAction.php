<?php

namespace App\Actions\Task;

use App\DataTransferObjects\Task\UpdateTaskDTO;
use App\Models\Task;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateTaskAction
{
    /**
     * @throws Exception
     */
    public function execute(Task $task, UpdateTaskDTO $dto): Task
    {
        DB::beginTransaction();

        try {
            $task->is_completed = $dto->is_completed;

            if ($dto->avatar) {
                $task->avatar = $dto->avatar;
            }

            $task->save();

            foreach ($task->translatable as $field) {
                if (property_exists($dto, $field)) {
                    $value = $dto->{$field};
                    $task->setTranslation($field, $value, $dto->language);
                }
            }

            DB::commit();

            return $task->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
