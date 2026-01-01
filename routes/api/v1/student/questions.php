<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Student\QuestionController;

Route::get('questions', [QuestionController::class, 'index'])->name('student.questions.index');
Route::post('questions/submit', [QuestionController::class, 'submit'])->name('student.questions.submit');
