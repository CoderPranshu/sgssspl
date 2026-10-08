<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePageMakerAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        abort_unless($user, 401);

        $allowedRole = method_exists($user, 'hasAnyRole')
            && $user->hasAnyRole(['Admin', 'SEO', 'SEO Team']);

        $allowedPermission = false;
        if (method_exists($user, 'getAllPermissions')) {
            $allowedPermission = $user->getAllPermissions()
                ->contains(fn ($permission) => $permission->name === 'manage pagemaker');
        }

        abort_unless($allowedRole || $allowedPermission, 403, 'You are not authorized to manage PageMaker pages.');

        return $next($request);
    }
}
