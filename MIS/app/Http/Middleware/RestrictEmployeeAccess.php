<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestrictEmployeeAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'employee') {
            // List of restricted URL paths (without slashes at the start)
            $restrictedPaths = [
                'dashboard/counter',
                'dashboard/accounts',
                'dashboard/reports',
                'dashboard/settings',
                'dashboard/manage-assignment',
                'dashboard/add-employee',
                'dashboard/manage-employee',
                'dashboard/add-bonus-deduction',
            ];

            // Get the current path (e.g. 'counter', 'manage-employee')
            $currentPath = $request->path(); // This will give you something like 'counter'

            if (in_array($currentPath, $restrictedPaths)) {
                abort(403, 'Unauthorized action.');
            }
        }

        return $next($request);
    }
}
