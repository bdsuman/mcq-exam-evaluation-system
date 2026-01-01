<?php

namespace App\Http\Controllers\Api\Task;

use App\Actions\Task\DestroyTaskAction;
use App\Actions\Task\StoreTaskAction;
use App\Actions\Task\ToggleTaskCompletedAction;
use App\Actions\Task\UpdateTaskAction;
use App\DataTransferObjects\Task\CreateTaskDTO;
use App\DataTransferObjects\Task\UpdateTaskDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Task\StoreTaskRequest;
use App\Http\Requests\Api\Task\UpdateTaskRequest;
use App\Http\Resources\Api\Task\TaskResource;
use App\Models\Task;
use App\Traits\UploadAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use function Laravel\Prompts\info;

/** 
 * @group Task Module
 * @authenticated
 */
class TaskController extends Controller
{
    use UploadAble;

    private const UPLOAD_OPTIONS = [
        'resize' => ['width' => 800, 'height' => 600],
        'quality' => 85
    ];

    /**
     * List
     *
     * @queryParam sort_by string The column to order by. Example: id
     * @queryParam sort_dir string Order direction (ASC|DESC). Example: DESC
     * @queryParam page integer Number of page. Example: 1.
     * @queryParam per_page integer Number of items per page. Example: 10.
     * @queryParam search string Search by title,author_name,job_title Example: Jhon
     * @queryParam publish boolean Filter by publish status. Example: true
     * @header X-Request-Language string Optional language for translated fields. Example: en
     *
     * @response 200 {
     *   "status": true,
     *   "message": "task_fetched_successfully",
     *     "data": [
     *              {
     *                  "id": 9,
     *                  "title": "Voluptatibus quidem minima atque.",
     *                  "description": "Temporibus tempora eum nesciunt doloremque. ae nostrum.",
     *                  "avatar": "https://placehold.co/100x100",
     *                  "avatar_url": "https://placehold.co/100x100",
     *                  "status": true,
     *                  "created_at": "2025-08-28T05:09:49.000000Z",
     *                  "updated_at": "2025-08-28T05:09:49.000000Z"
     *              }
     *             ],
     *   "meta": {
     *     "total": 40,
     *     "last_page": 40,
     *     "per_page": 1,
     *     "current_page": 1
     *   }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 10), 1), 100);
        $userId = $request->user()->id;
        $cacheKey = "tasks_user_{$userId}_page_{$request->input('page', 1)}_per_{$perPage}";

        $tasks = cache()->remember($cacheKey, 3600, function () use ($request, $perPage, $userId) {
            return Task::query()
                ->where('user_id', $userId)
                ->isCompletedFilter($request->is_completed)
                ->searchFilter($request->search)
                ->orderBy('id', 'DESC')
                ->paginate($perPage);
        });

        return success_response(TaskResource::collection($tasks), true, 'task_fetched_successfully');
    }

    /**
     * Store
     *
     * @param StoreTaskRequest $request
     * @return JsonResponse
     *
     * @header X-Request-Language string Optional language for translated fields. Example: en
     * @response 201 {
     *  "status": true,
     *  "message": "success_task_created",
     *  "data": {
     *      "id": 21,
     *      "user_id": 1,
     *      "title": "title",
     *      "description": "content",
     *      "is_completed": true,
     *      "avatar": "task/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "avatar_url": "http://localhost:8000/storage/task/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "created_at": "2025-08-28T08:49:59.000000Z",
     *      "updated_at": "2025-08-28T08:49:59.000000Z"
     *  }
     * }
     */
    public function store(StoreTaskRequest $request, StoreTaskAction $action): JsonResponse
    {
        try {
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $this->uploadFile(
                    $request->file('avatar'),
                    'tasks/avatar',
                    config('filesystems.default'),
                    null,
                    self::UPLOAD_OPTIONS
                );
            }

            $dto = new CreateTaskDTO(
                user_id: $request->user()->id,
                title: $request->input('title'),
                description: $request->input('description'),
                is_completed: $request->boolean('is_completed'),
                avatar: $avatarPath,
            );
            info('Created CreateTaskDTO', ['dto' => $dto]);

            $task = $action->execute($dto);

            return success_response(
                new TaskResource($task),
                false,
                'success_task_created',
                201
            );
        } catch (Exception $e) {
            return error_response('task_creation_failed', 500);
        }
    }

    /**
     * Show
     *
     * @param Task $task
     *
     * @header X-Request-Language string Optional language for translated fields. Example: en
     * @response 200 {
     *   "status": true,
     *   "message": "task_fetched_successfully",
     *     "data": {
     *                  "id": 9,
     *                  "user_id": 1,
     *                  "title": "Voluptatibus quidem minima atque.",
     *                  "description": "Temporibus tempora eum nesciunt doloremque. ae nostrum.",
     *                  "is_completed": true,
     *                  "avatar": "https://placehold.co/100x100",
     *                  "avatar_url": "https://placehold.co/100x100",
     *                  "created_at": "2025-08-28T05:09:49.000000Z",
     *                  "updated_at": "2025-08-28T05:09:49.000000Z"
     *              }
     * }
     */
    public function show(Task $task): JsonResponse
    {
        return success_response(
            new TaskResource($task),
            false,
            'task_fetched_successfully'
        );
    }

    /**
     * Update
     *
     * @param UpdateTaskRequest $request
     * @param Task $task
     * @return JsonResponse
     *
     * @header X-Request-Language string Optional language for translated fields. Example: en
     * @response 200 {
     *  "status": true,
     *  "message": "success_task_updated",
     *  "data": {
     *      "id": 21,
     *      "user_id": 1,
     *      "title": "Outstanding Support!",
     *      "description": "The team provided exceptional support throughout the project.",
     *      "avatar": "task/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "avatar_url": "http://localhost:8000/storage/task/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "is_completed": true,
     *      "created_at": "2025-08-28T08:49:59.000000Z",
     *      "updated_at": "2025-08-28T08:49:59.000000Z"
     *  }
     * }
     */
    public function update(UpdateTaskRequest $request, Task $task, UpdateTaskAction $action): JsonResponse
    {
        try {
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                // Delete old file if exists
                if ($task->avatar) {
                    $this->deleteFile($task->avatar, config('filesystems.default'));
                }

                $avatarPath = $this->uploadFile(
                    $request->file('avatar'),
                    'tasks/avatar',
                    config('filesystems.default'),
                    null,
                    self::UPLOAD_OPTIONS
                );
            }

            $dto = new UpdateTaskDTO(
                title: $request->input('title'),
                description: $request->input('description'),
                is_completed: $request->boolean('is_completed'),
                language: $request->input('language', app('language')),
                avatar: $avatarPath,
            );

            $updatedTask = $action->execute($task, $dto);

            return success_response(
                new TaskResource($updatedTask),
                false,
                'success_task_updated'
            );
        } catch (Exception $e) {
            return error_response('task_update_failed', 500);
        }
    }

    /**
     * Delete
     *
     * Soft delete the specified task.  
     * The record is not permanently removed; instead, its `deleted_at` timestamp is set  
     * and the `title` & `author_name` is updated with a `DELETED_` prefix.
     *
     * @param Task $task
     * @header X-Request-Language string Optional language for translated fields. Example: en
     *
     * @response 200 {
     *   "status": true,
     *   "message": "success_task_deleted",
     *   "data": []
     * }
     */
    public function destroy(Task $task, DestroyTaskAction $action): JsonResponse
    {
        $action->execute($task);

        return success_response([], false, 'success_task_deleted');
    }

    /**
     * Toggle Completed
     *
     * This endpoint switches the `publish` status of the given Task.
     * If it was published, it will be unpublished, and vice versa.
     *
     * @param Task $task
     * @header X-Request-Language string Optional language for translated fields. Example: en
     *
     * @response 200 {
     *  "status": true,
     *  "message": "success_task_updated",
     *  "data": {
     *      "id": 21,
     *      "author_name": "Jane Smith",
     *      "job_title": "Product Manager",
     *      "title": "Outstanding Support!",
     *      "description": "The team provided exceptional support throughout the project.",
     *      "is_completed": true,
     *      "avatar": "task/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "avatar_url": "http://localhost:8000/storage/task/avatar/FILE_68b018378dc0e_68b01837.png",
     *      "created_at": "2025-08-28T08:49:59.000000Z",
     *      "updated_at": "2025-08-28T08:49:59.000000Z"
     *  }
     * }
     */
    public function toggleCompleted(Task $task, ToggleTaskCompletedAction $action): JsonResponse
    {
        $updatedTask = $action->execute($task);

        return success_response(
            new TaskResource($updatedTask),
            false,
            'success_task_updated'
        );
    }
}
