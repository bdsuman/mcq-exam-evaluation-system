<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Task\TaskController;

Route::apiResource('tasks', TaskController::class);
Route::put('/tasks/completed/{task}', [TaskController::class, 'toggleCompleted'])->name('tasks.completed');