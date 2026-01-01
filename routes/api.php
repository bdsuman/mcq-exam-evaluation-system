<?php

use Illuminate\Support\Facades\Route;

/******************************************For Web App API ******************************************************/

// Group all routes under /v1 prefix
Route::prefix('v1')->group(function () {

    // Without Authentication (admin endpoints)
    Route::prefix('admin')->group(function () {
        require __DIR__ . '/api/v1/admin/without_authentication.php';
    });

    // Authenticated routes
    Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
        require __DIR__ . '/api/v1/admin/auth.php';
        require __DIR__ . '/api/v1/admin/tasks.php';
        require __DIR__ . '/api/v1/admin/enum.php';
    });
});

