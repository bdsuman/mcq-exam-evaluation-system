<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ScribeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (config('scribe_mobile.laravel.add_routes', false)) {
            $this->registerMobileDocRoutes();
        }
    }

    /**
     * Register mobile documentation routes.
     */
    protected function registerMobileDocRoutes(): void
    {
        $config = config('scribe_mobile');
        $docsUrl = $config['laravel']['docs_url'] ?? '/docs/mobile';
        $middleware = $config['laravel']['middleware'] ?? [];

        Route::group([
            'middleware' => $middleware,
        ], function () use ($docsUrl) {
            // Main mobile docs route
            Route::get($docsUrl, function () {
                return view('scribe_mobile.index');
            })->name('scribe_mobile');

            // Mobile Postman collection route
            Route::get($docsUrl . '.postman', function () {
                $collection = storage_path('app/private/scribe_mobile/collection.json');
                if (!file_exists($collection)) {
                    abort(404, 'Postman collection not found. Run php artisan scribe:generate --config=scribe_mobile');
                }
                return response()->file($collection, [
                    'Content-Type' => 'application/json',
                    'Content-Disposition' => 'attachment; filename="mobile-api-collection.json"'
                ]);
            })->name('scribe_mobile.postman');

            // Mobile OpenAPI spec route
            Route::get($docsUrl . '.openapi', function () {
                $spec = storage_path('app/private/scribe_mobile/openapi.yaml');
                if (!file_exists($spec)) {
                    abort(404, 'OpenAPI spec not found. Run php artisan scribe:generate --config=scribe_mobile');
                }
                return response()->file($spec, [
                    'Content-Type' => 'application/x-yaml',
                    'Content-Disposition' => 'attachment; filename="mobile-api-spec.yaml"'
                ]);
            })->name('scribe_mobile.openapi');
        });
    }
}
