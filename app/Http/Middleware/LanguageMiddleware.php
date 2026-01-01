<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->header('language');

        if (!$request->hasHeader('language') || empty($request->header('language'))) {
            return response()->json([
                'success' => false,
                'message' => 'missing_required_language_in_header',
            ], 422);
        }

        app()->singleton('language', fn() => $lang);
        app()->setLocale($lang);

        return $next($request);
    }
}
