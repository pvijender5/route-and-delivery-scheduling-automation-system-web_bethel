<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }
    // public function handle($request, Closure $next, $role)
    // {
    //     // Check if the user has the specified role
    //     if ($request->user() && $request->user()->role->name === $role) {
    //         return $next($request);
    //     }

    //     // If the user doesn't have the required role, redirect or show an error
    //     abort(403, 'Unauthorized action.');
    // }
    // public function handle($request, Closure $next, ...$roles)
    // {
    //     //dd($roles);
    //     // Check if the authenticated user has any of the specified roles
    //     if (!auth()->user()->hasAnyRole($roles)) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     return $next($request);
    // }
    // public function handle($request, Closure $next, ...$roles)
    // {
    //     // Get all roles from the database
    //     $allRoles = Role::pluck('id','name')->toArray();
    //     // Check if the user has any of the required roles
    //     if (!auth()->user()->hasAnyRole($roles) || !in_array($roles, $allRoles)) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     return $next($request);
    // }
    public function handle($request, Closure $next, ...$roles)
    {
        // if ($request->user()->role->name === $role) {
        //     return $next($request);
        // }
        if ($request->user() && $request->user()->role && in_array($request->user()->role->name, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }


}
