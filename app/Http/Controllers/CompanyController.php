<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class CompanyController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:companies.view')->only(['index', 'show']);
        $this->middleware('permission:companies.create')->only(['create', 'store']);
        $this->middleware('permission:companies.edit')->only(['edit', 'update']);
        $this->middleware('permission:companies.delete')->only(['destroy']);
    }

    /**
     * Display a listing of companies (super admin only)
     */
    public function index()
    {
        $companies = Company::with(['users', 'packages'])
            ->withCount(['users', 'packages'])
            ->orderBy('name')
            ->paginate(15);

        return view('companies.index', compact('companies'));
    }

    /**
     * Show company details
     */
    public function show(Company $company)
    {
        $company->load(['users.roles', 'packages', 'roles']);

        return view('companies.show', compact('company'));
    }

    /**
     * Show form for creating a new company
     */
    /**
     * Show form for creating a new company
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created company
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:companies,email',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
            'timezone' => 'nullable|string|max:50',
        ]);

        // Set default status if not provided
        if (!isset($validated['status'])) {
            $validated['status'] = 'active';
        }

        $company = Company::create($validated);

        // Create default roles for the company
        $this->createDefaultRoles($company);

        return redirect()->route('companies.index')
            ->with('success', 'Company created successfully.');
    }

    /**
     * Show form for editing a company
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update a company
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('companies')->ignore($company->id)
            ],
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
            'timezone' => 'nullable|string|max:50',
        ]);

        $company->update($validated);

        return redirect()->route('companies.index')
            ->with('success', 'Company updated successfully.');
    }

    /**
     * Delete a company
     */
    public function destroy(Company $company)
    {
        // Prevent deletion if company has users or packages
        if ($company->users()->count() > 0 || $company->packages()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete company with existing users or packages.');
        }

        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }

    /**
     * Switch current company context (for super admins)
     */
    public function switchCompany(Request $request, Company $company)
    {
        if (!Auth::user()->is_super_admin) {
            abort(403, 'Only super admins can switch companies.');
        }

        session(['current_company_id' => $company->id]);

        return redirect()->back()
            ->with('success', "Switched to {$company->name} context.");
    }

    /**
     * Create default roles for a new company
     */
    private function createDefaultRoles(Company $company)
    {
        $defaultRoles = [
            [
                'name' => 'Company Admin',
                'slug' => 'company-admin',
                'description' => 'Full access to company operations',
                'permissions' => [
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'packages.view', 'packages.create', 'packages.edit', 'packages.delete',
                    'packages.bulk_operations',
                    'reports.view', 'reports.export',
                    'company.manage', 'settings.manage'
                ],
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manages day-to-day operations',
                'permissions' => [
                    'users.view', 'users.edit',
                    'packages.view', 'packages.create', 'packages.edit',
                    'packages.bulk_operations',
                    'reports.view'
                ],
            ],
            [
                'name' => 'Employee',
                'slug' => 'employee',
                'description' => 'Handles package operations',
                'permissions' => [
                    'packages.view', 'packages.create', 'packages.edit',
                    'dashboard.view'
                ],
            ],
            [
                'name' => 'Client',
                'slug' => 'client',
                'description' => 'Limited access for clients',
                'permissions' => [
                    'packages.view',
                    'dashboard.view'
                ],
            ],
        ];

        foreach ($defaultRoles as $roleData) {
            Role::create(array_merge($roleData, [
                'company_id' => $company->id,
                'is_system_role' => false,
            ]));
        }
    }
}
