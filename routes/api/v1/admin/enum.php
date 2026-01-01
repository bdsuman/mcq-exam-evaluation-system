<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Common\EnumController;

Route::get('enums/gender', [EnumController::class, 'genderOptions'])->name('enums.gender');
Route::get('enums/question-type', [EnumController::class, 'questionTypeOptions'])->name('enums.question-type');

