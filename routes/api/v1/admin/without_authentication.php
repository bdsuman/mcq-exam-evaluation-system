<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('google-login', [AuthController::class, 'googleLogin'])->name('google-login');
Route::post('register', [AuthController::class, 'register'])->name('register');
