<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip company checks for super admins
        if (!$user || $user->is_super_admin) {
            return $next($request);
        }

        // Ensure user has a company assigned
        if (!$user->company_id) {
            abort(403, 'No company assigned to your account. Please contact an administrator.');
        }

        // Set current company context in session if not set
        if (!session('current_company_id')) {
            session(['current_company_id' => $user->company_id]);
        }

        // Validate that session company matches user's company (security check)
        $sessionCompanyId = session('current_company_id');
        if ($sessionCompanyId && $sessionCompanyId !== $user->company_id) {
            // Reset to user's actual company
            session(['current_company_id' => $user->company_id]);
        }

        return $next($request);
    }
}
