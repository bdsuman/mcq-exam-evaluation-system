<?php

use Illuminate\Support\Facades\Route;

/******************************************For Web App API ******************************************************/

// Group all routes under /v1 prefix
Route::prefix('v1')->group(function () {

    // Without Authentication (any endpoints)
    require __DIR__ . '/api/v1/admin/without_authentication.php';


    // Authenticated routes
    Route::middleware(['auth:sanctum'])->group(function () {
        require __DIR__ . '/api/v1/admin/auth.php';
        require __DIR__ . '/api/v1/admin/enum.php';
    });

    // Admin routes
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
        require __DIR__ . '/api/v1/admin/dashboard.php';
        require __DIR__ . '/api/v1/admin/questions.php';
    });

    // Student routes
    Route::middleware(['auth:sanctum', 'role:student'])->prefix('student')->group(function () {
        require __DIR__ . '/api/v1/student/questions.php';
    });
});

