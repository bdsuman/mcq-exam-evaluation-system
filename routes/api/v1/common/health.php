<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0'),
    ]);
});

Route::get('/status', function () {
    try {
        // Check database connection
        DB::connection()->getPdo();
        $dbStatus = 'connected';
    } catch (\Exception $e) {
        $dbStatus = 'disconnected';
    }

    return response()->json([
        'status' => 'ok',
        'services' => [
            'database' => $dbStatus,
            'cache' => cache()->store()->getStore() ? 'connected' : 'disconnected',
        ],
        'timestamp' => now()->toISOString(),
        'environment' => app()->environment(),
    ]);
});


// get all routes link with method 
Route::get('/routes', function () {
    $routes = collect(Route::getRoutes())->map(function ($route) {
        return [
            'uri' => $route->uri,
            'methods' => $route->methods(),
            'name' => $route->getName(),
        ];
    });

    return response()->json($routes);
});
