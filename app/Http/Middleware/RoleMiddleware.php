<?php

namespace App\Http\Middleware;

use App\Enums\UserRoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return error_response('unauthenticated', 401);
        }

        $allowedRole = $role instanceof UserRoleEnum ? $role->value : $role;
        if ($user->role->value !== $allowedRole) {
            return error_response('forbidden', 403);
        }

        return $next($request);
    }
}
