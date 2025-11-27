<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Company;

class CompanyScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip for super admins - they can access all companies
        if (Auth::check() && Auth::user()->is_super_admin) {
            // For super admins, set the company from request or session
            $companyId = $request->get('company_id') ?? session('selected_company_id');
            if ($companyId) {
                $company = Company::find($companyId);
                if ($company) {
                    $this->setCompanyContext($company);
                }
            }
            return $next($request);
        }

        // For regular users, ensure they have access to their company
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user has a company
            if (!$user->company_id) {
                abort(403, 'No company assigned to user');
            }

            // Set company context
            $company = $user->company;
            if (!$company) {
                abort(403, 'Invalid company');
            }

            // Check if company is active
            if (!$company->isActive()) {
                abort(403, 'Company is not active');
            }

            $this->setCompanyContext($company);
        }

        return $next($request);
    }

    /**
     * Set the company context for the application
     */
    private function setCompanyContext(Company $company)
    {
        // Store company in session for current request
        session(['current_company_id' => $company->id]);

        // Share company with all views
        View::share('currentCompany', $company);

        // Set global query scope for company-scoped models
        // This will be handled by model scopes instead
    }
}
