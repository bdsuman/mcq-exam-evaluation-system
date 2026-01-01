<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust proxy headers from reverse proxy (nginx)
        // This tells Laravel to trust X-Forwarded-* headers from all proxies
        $middleware->trustProxies('*');

        // Register route middleware (alias → middleware class)
        $middleware->alias([
            // 'language.header' => \App\Http\Middleware\LanguageMiddleware::class,
        ]);

        // Apply LanguageMiddleware middleware to API group
        $middleware->prependToGroup('api', \App\Http\Middleware\LanguageMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 401 from guards (no user / not authenticated)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return error_response('unauthenticated', 401);
        });

        // Rejections thrown by JWT middleware (blacklisted/expired/invalid/missing)
        $exceptions->render(function (UnauthorizedHttpException $e, Request $request) {

            $prev = $e->getPrevious();
            // Generic unauthorized
            return error_response('unauthorized', 401);
        });
        $exceptions->render(function (Throwable $e, $request) {

            if ($request->expectsJson()) {
                if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                    return error_response('not_found', 404);
                }

                if ($e instanceof QueryException && !config('app.debug')) {
                    return error_response('something_went_wrong_while_fetching_data', 500);
                }
            }
        });
    })
    ->withCommands([
        App\Console\Commands\GenerateApiDocs::class,
    ])->create();
