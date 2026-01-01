<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Common\EnumController;

Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('user-details', [AuthController::class, 'me'])->name('me');
Route::put('update-profile', [AuthController::class, 'updateProfile'])->name('update-profile');
Route::put('change-password', [AuthController::class, 'changePassword'])->name('change-password');
Route::get('enums/gender', [EnumController::class, 'genderOptions'])->name('enums.gender');

