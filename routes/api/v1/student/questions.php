<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Student\QuestionController;
use App\Http\Controllers\Api\Student\SubmissionController;

Route::get('questions', [QuestionController::class, 'index'])->name('student.questions.index');
Route::post('questions/submit', [QuestionController::class, 'submit'])->name('student.questions.submit');
Route::get('questions/{question}/submission', [QuestionController::class, 'submission'])->name('student.questions.submission');
Route::get('questions/stats', [SubmissionController::class, 'stats'])->name('student.questions.stats');
