<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionDeniedRedirect
{
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (auth()->check() && !auth()->user()->can($permission)) {

            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
