<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.view')->only(['index', 'show']);
        $this->middleware('permission:users.create')->only(['create', 'store']);
        $this->middleware('permission:users.edit')->only(['edit', 'update']);
        $this->middleware('permission:users.delete')->only(['destroy']);
    }

    /**
     * Display a listing of users
     */
    public function index()
    {
        $currentUser = Auth::user();
        $query = User::with(['company', 'userRoles.role']);

        // Super admins see all users, others see only their company
        if (!$currentUser->is_super_admin) {
            $companyId = session('current_company_id') ?? $currentUser->company_id;
            $query->where('company_id', $companyId);
        }

        $users = $query->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        $this->authorizeUserAccess($user);

        $user->load(['company', 'userRoles.role', 'packages']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show form for creating a new user
     */
    public function create()
    {
        $currentUser = Auth::user();
        $companies = collect();
        $roles = collect();

        if ($currentUser->is_super_admin) {
            $companies = Company::where('status', 'active')->get();
        } else {
            $companyId = session('current_company_id') ?? $currentUser->company_id;
            $companies = Company::where('id', $companyId)->get();
        }

        // Get roles for the current company context
        if ($companies->isNotEmpty()) {
            $companyId = $companies->first()->id;
            $roles = Role::where('company_id', $companyId)->get();
        }

        return view('admin.users.create', compact('companies', 'roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_id' => 'required|exists:companies,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        // Validate company access
        if (!$currentUser->is_super_admin) {
            $allowedCompanyId = session('current_company_id') ?? $currentUser->company_id;
            if ($validated['company_id'] != $allowedCompanyId) {
                abort(403, 'Cannot create users for other companies.');
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => $validated['company_id'],
        ]);

        // Assign roles
        if (isset($validated['roles'])) {
            foreach ($validated['roles'] as $roleId) {
                $user->assignRole($roleId, $validated['company_id']);
            }
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show form for editing user
     */
    public function edit(User $user)
    {
        $this->authorizeUserAccess($user);

        $currentUser = Auth::user();
        $companies = collect();

        if ($currentUser->is_super_admin) {
            $companies = Company::where('status', 'active')->get();
        } else {
            $companies = Company::where('id', $user->company_id)->get();
        }

        $roles = Role::where('company_id', $user->company_id)->get();
        $userRoles = $user->rolesInCompany($user->company_id)->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'companies', 'roles', 'userRoles'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeUserAccess($user);

        $currentUser = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'company_id' => 'required|exists:companies,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        // Validate company access
        if (!$currentUser->is_super_admin) {
            $allowedCompanyId = session('current_company_id') ?? $currentUser->company_id;
            if ($validated['company_id'] != $allowedCompanyId || $user->company_id != $allowedCompanyId) {
                abort(403, 'Cannot modify users from other companies.');
            }
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company_id' => $validated['company_id'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Update roles
        $user->userRoles()->where('company_id', $user->company_id)->delete();
        if (isset($validated['roles'])) {
            foreach ($validated['roles'] as $roleId) {
                $user->assignRole($roleId, $user->company_id);
            }
        }

        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        $this->authorizeUserAccess($user);

        // Prevent deletion of super admin users by non-super admins
        if ($user->is_super_admin && !Auth::user()->is_super_admin) {
            abort(403, 'Cannot delete super admin users.');
        }

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Authorize access to user based on company permissions
     */
    private function authorizeUserAccess(User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->is_super_admin) {
            return; // Super admins can access all users
        }

        $allowedCompanyId = session('current_company_id') ?? $currentUser->company_id;

        if ($user->company_id !== $allowedCompanyId) {
            abort(403, 'Cannot access users from other companies.');
        }
    }
}
