<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Super admins have all permissions
        if ($user->is_super_admin) {
            return $next($request);
        }

        // Get current company ID from session
        $companyId = session('current_company_id') ?? $user->company_id;

        // Check if user has the required permission in the current company
        if (!$user->hasPermission($permission, $companyId)) {
            abort(403, "Access denied. Required permission: {$permission}");
        }

        return $next($request);
    }
}
