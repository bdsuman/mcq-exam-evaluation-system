<?php

namespace App\Actions\Task;

use App\DataTransferObjects\Task\CreateTaskDTO;
use App\Enums\AppLanguageEnum;
use App\Models\Task;
use Exception;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\info;

class StoreTaskAction
{
    /**
     * @throws Exception
     */
    public function execute(CreateTaskDTO $dto): Task
    {
        info('StoreTaskAction called');
        DB::beginTransaction();

        try {
            $task = new Task();
            $task->user_id = $dto->user_id;
            $task->is_completed = $dto->is_completed;

            if ($dto->avatar) {
                $task->avatar = $dto->avatar;
            }

            $task->save();

            foreach ($task->translatable as $field) {
                if (property_exists($dto, $field)) {
                    $value = $dto->{$field};
                    foreach (AppLanguageEnum::cases() as $langEnum) {
                        $task->setTranslation($field, $value, $langEnum->value);
                    }
                }
            }

            DB::commit();

            return $task;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
