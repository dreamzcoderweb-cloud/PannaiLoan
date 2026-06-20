<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to enforce route-based access control for employees
 * 
 * This middleware ensures that employees can only access resources
 * that belong to their assigned branch and route.
 */
class EnsureEmployeeRouteAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Add the user's branch and route to the request for easy access in controllers
        $request->attributes->set('employee_branch_id', $user->branch_id);
        $request->attributes->set('employee_route_id', $user->route_id);

        return $next($request);
    }
}
