<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Question\QuestionController;

Route::apiResource('questions', QuestionController::class);
