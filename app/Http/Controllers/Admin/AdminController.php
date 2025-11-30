<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Package;
use App\Services\PackageWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;

class AdminController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $workflowService;

    public function __construct(PackageWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->is_super_admin) {
                abort(403, 'Access denied. Super admin privileges required.');
            }
            return $next($request);
        });
    }

    /**
     * Show the admin dashboard
     */
    public function dashboard()
    {
        $stats = $this->getSystemStats();
        $recentCompanies = Company::latest()->take(5)->get();

        // Return JSON for AJAX requests, view for regular requests
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(compact('stats', 'recentCompanies'));
        }

        return view('admin.dashboard', compact('stats', 'recentCompanies'));
    }

    /**
     * Get comprehensive system statistics
     */
    private function getSystemStats()
    {
        // Global system statistics
        $totalCompanies = Company::where('status', 'active')->count();
        $totalUsers = User::whereHas('company', function($q) {
            $q->where('status', 'active');
        })->count();
        $totalPackages = Package::count();
        $recentPackages = Package::where('created_at', '>=', now()->subDays(7))->count();

        // Package status breakdown
        $pendingPackages = Package::where('status', 'Incoming')->count();
        $readyPackages = Package::where('status', 'Ready for Pickup')->count();
        $pickedUpPackages = Package::where('status', 'Picked Up')->count();
        $archivedPackages = Package::where('status', 'Archived')->count();

        // Workflow statistics
        $workflowStats = $this->workflowService->getWorkflowStats();

        return [
            'company_stats' => [
                'total_companies' => $totalCompanies,
                'total_users' => $totalUsers,
            ],
            'total_packages' => $totalPackages,
            'incoming_packages' => $pendingPackages,
            'ready_packages' => $readyPackages,
            'picked_up_packages' => $pickedUpPackages,
            'archived_packages' => $archivedPackages,
            'recent_packages' => $recentPackages,
            'workflow_stats' => $workflowStats,
            'aging_packages' => $this->workflowService->getAgingPackages()->count(),
        ];
    }

    /**
     * Show system reports
     */
    public function reports()
    {
        // Company performance metrics
        $companyStats = Company::withCount(['users', 'packages'])->get()->map(function($company) {
            $recentPackages = Package::where('company_id', $company->id)
                ->where('created_at', '>=', now()->subDays(30))->count();

            $avgProcessingTime = Package::where('company_id', $company->id)
                ->whereNotNull('picked_up_at')
                ->whereNotNull('received_at')
                ->where('picked_up_at', '>=', Carbon::now()->subDays(30))
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, received_at, picked_up_at)) as avg_hours')
                ->first();

            return [
                'id' => $company->id,
                'name' => $company->name,
                'users_count' => $company->users_count,
                'packages_count' => $company->packages_count,
                'recent_packages' => $recentPackages,
                'avg_processing_time' => round($avgProcessingTime->avg_hours ?? 0, 1),
                'status' => $company->status,
                'created_at' => $company->created_at->format('Y-m-d')
            ];
        });

        // Package processing trends (last 30 days)
        $packageTrends = Package::selectRaw('DATE(created_at) as date, COUNT(*) as count, status')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(['date', 'status'])
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        // Status distribution
        $statusDistribution = Package::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Performance metrics
        $performanceMetrics = [
            'total_packages_today' => Package::whereDate('created_at', Carbon::today())->count(),
            'packages_picked_up_today' => Package::where('status', 'Picked Up')
                ->whereDate('picked_up_at', Carbon::today())->count(),
            'aging_packages' => $this->workflowService->getAgingPackages()->count(),
            'average_processing_time' => $this->workflowService->getWorkflowStats()['average_processing_time'],
        ];

        // Recent activity (last 24 hours)
        $recentActivity = Package::with(['company'])
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function($package) {
                return [
                    'id' => $package->id,
                    'tracking_number' => $package->tracking_number,
                    'company' => $package->company->name ?? 'Unknown',
                    'status' => $package->status,
                    'created_at' => $package->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'companyStats' => $companyStats,
            'packageTrends' => $packageTrends,
            'statusDistribution' => $statusDistribution,
            'performanceMetrics' => $performanceMetrics,
            'recentActivity' => $recentActivity
        ]);
    }

    /**
     * System settings management
     */
    public function settings()
    {
        // Get system configuration
        $settings = [
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
                'debug' => config('app.debug'),
                'timezone' => config('app.timezone'),
                'url' => config('app.url'),
            ],
            'database' => [
                'default' => config('database.default'),
                'connection_status' => $this->checkDatabaseHealth()['status'],
            ],
            'mail' => [
                'driver' => Config::get('mail.default', 'smtp'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ],
            'cache' => [
                'driver' => config('cache.default'),
                'status' => $this->checkCacheHealth()['status'],
            ],
            'session' => [
                'driver' => config('session.driver'),
                'lifetime' => config('session.lifetime'),
            ],
            'filesystem' => [
                'default' => config('filesystems.default'),
                'status' => $this->checkStorageHealth()['status'],
            ],
            'workflow' => [
                'auto_transition_enabled' => true,
                'aging_threshold_days' => 7,
                'notification_enabled' => true,
            ]
        ];

        return response()->json(compact('settings'));
    }

    /**
     * System health check
     */
    public function healthCheck()
    {
        $health = [
            'database' => $this->checkDatabaseHealth(),
            'cache' => $this->checkCacheHealth(),
            'storage' => $this->checkStorageHealth(),
            'mail' => $this->checkMailHealth(),
            'workflow_service' => $this->checkWorkflowHealth(),
            'queue' => $this->checkQueueHealth(),
            'disk_space' => $this->checkDiskSpace(),
            'memory_usage' => $this->checkMemoryUsage(),
        ];

        // Overall system health
        $overallHealth = collect($health)->every(function($check) {
            return $check['status'] === 'healthy';
        });

        $health['overall_status'] = $overallHealth ? 'healthy' : 'warning';

        return response()->json($health);
    }

    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Database connection failed: ' . $e->getMessage()];
        }
    }

    private function checkCacheHealth()
    {
        try {
            cache()->put('health_check', 'test', 1);
            $value = cache()->get('health_check');
            cache()->forget('health_check');

            return ['status' => $value === 'test' ? 'healthy' : 'unhealthy', 'message' => 'Cache system operational'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Cache system error: ' . $e->getMessage()];
        }
    }

    private function checkStorageHealth()
    {
        try {
            $testFile = 'health_check_' . time() . '.txt';
            Storage::put($testFile, 'test');
            $content = Storage::get($testFile);
            Storage::delete($testFile);

            return ['status' => $content === 'test' ? 'healthy' : 'unhealthy', 'message' => 'Storage system operational'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Storage system error: ' . $e->getMessage()];
        }
    }

    private function checkMailHealth()
    {
        try {
            // Just check if mail configuration is properly set
            $driver = Config::get('mail.default', 'smtp');
            return ['status' => 'healthy', 'message' => "Mail driver: $driver"];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Mail configuration error: ' . $e->getMessage()];
        }
    }

    private function checkWorkflowHealth()
    {
        try {
            $stats = $this->workflowService->getWorkflowStats();
            $agingCount = $this->workflowService->getAgingPackages()->count();

            return [
                'status' => 'healthy',
                'message' => "Workflow service operational. {$agingCount} aging packages",
                'details' => $stats
            ];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Workflow service error: ' . $e->getMessage()];
        }
    }

    private function checkQueueHealth()
    {
        try {
            // Check if queue driver is configured
            $driver = config('queue.default');
            return ['status' => 'healthy', 'message' => "Queue driver: $driver"];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Queue configuration error: ' . $e->getMessage()];
        }
    }

    private function checkDiskSpace()
    {
        try {
            $freeBytes = disk_free_space(storage_path());
            $totalBytes = disk_total_space(storage_path());
            $freeGB = round($freeBytes / 1024 / 1024 / 1024, 2);
            $totalGB = round($totalBytes / 1024 / 1024 / 1024, 2);
            $usedPercent = round((($totalBytes - $freeBytes) / $totalBytes) * 100, 1);

            $status = $usedPercent > 90 ? 'warning' : 'healthy';
            $message = "Disk usage: {$usedPercent}% ({$freeGB}GB free of {$totalGB}GB)";

            return ['status' => $status, 'message' => $message];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Unable to check disk space: ' . $e->getMessage()];
        }
    }

    private function checkMemoryUsage()
    {
        try {
            $memoryUsage = memory_get_usage(true);
            $memoryLimit = ini_get('memory_limit');

            // Convert memory limit to bytes
            $memoryLimitBytes = $this->convertToBytes($memoryLimit);
            $memoryUsageMB = round($memoryUsage / 1024 / 1024, 2);
            $memoryLimitMB = round($memoryLimitBytes / 1024 / 1024, 2);
            $usagePercent = round(($memoryUsage / $memoryLimitBytes) * 100, 1);

            $status = $usagePercent > 80 ? 'warning' : 'healthy';
            $message = "Memory usage: {$usagePercent}% ({$memoryUsageMB}MB of {$memoryLimitMB}MB)";

            return ['status' => $status, 'message' => $message];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Unable to check memory usage: ' . $e->getMessage()];
        }
    }

    private function convertToBytes($value)
    {
        $unit = strtolower(substr($value, -1));
        $value = (int) $value;

        switch ($unit) {
            case 'g': return $value * 1024 * 1024 * 1024;
            case 'm': return $value * 1024 * 1024;
            case 'k': return $value * 1024;
            default: return $value;
        }
    }

    /**
     * User management functions
     */
    public function users()
    {
        $users = User::with(['company', 'userRoles.role'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($user) {
                // Get user's role in their company
                $roleInCompany = $user->userRoles()
                    ->where('company_id', $user->company_id)
                    ->where('is_active', true)
                    ->with('role')
                    ->first();

                $roleName = 'No Role';
                if ($user->is_super_admin) {
                    $roleName = 'Super Admin';
                } elseif ($roleInCompany && $roleInCompany->role) {
                    $roleName = $roleInCompany->role->name;
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'company' => $user->company->name ?? 'No Company',
                    'is_super_admin' => $user->is_super_admin,
                    'role' => $roleName,
                    'last_login' => $user->last_login_at ? Carbon::parse($user->last_login_at)->diffForHumans() : 'Never',
                    'created_at' => $user->created_at->format('Y-m-d H:i')
                ];
            });

        return response()->json(['users' => $users]);
    }

    /**
     * Company management functions
     */
    public function companies()
    {
        $companies = Company::withCount(['users', 'packages'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($company) {
                $recentPackages = Package::where('company_id', $company->id)
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count();

                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'status' => $company->status,
                    'users_count' => $company->users_count,
                    'packages_count' => $company->packages_count,
                    'recent_packages' => $recentPackages,
                    'created_at' => $company->created_at->format('Y-m-d H:i')
                ];
            });

        return response()->json(['companies' => $companies]);
    }

    /**
     * Package analytics
     */
    public function packageAnalytics()
    {
        // Package status distribution
        $statusDistribution = Package::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Processing time analysis
        $processingTimes = Package::whereNotNull('picked_up_at')
            ->whereNotNull('received_at')
            ->where('picked_up_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('
                tracking_number,
                company_id,
                TIMESTAMPDIFF(HOUR, received_at, picked_up_at) as processing_hours
            ')
            ->orderBy('processing_hours', 'desc')
            ->take(50)
            ->get();

        // Daily package trends (last 30 days)
        $dailyTrends = Package::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Package aging analysis
        $agingPackages = $this->workflowService->getAgingPackages()
            ->map(function($package) {
                return [
                    'id' => $package->id,
                    'tracking_number' => $package->tracking_number,
                    'company' => $package->company->name ?? 'Unknown',
                    'ready_at' => $package->ready_at ? Carbon::parse($package->ready_at)->diffForHumans() : null,
                    'age_days' => $package->ready_at ? Carbon::parse($package->ready_at)->diffInDays(Carbon::now()) : null,
                ];
            });

        return response()->json([
            'statusDistribution' => $statusDistribution,
            'processingTimes' => $processingTimes,
            'dailyTrends' => $dailyTrends,
            'agingPackages' => $agingPackages
        ]);
    }

    /**
     * Create new user
     */
    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_id' => 'required|exists:companies,id',
            'is_super_admin' => 'boolean',
            'role' => 'required_unless:is_super_admin,1|in:admin,user'
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'company_id' => $request->company_id,
                'is_super_admin' => $request->boolean('is_super_admin', false)
            ]);

            // Assign role if not super admin
            if (!$user->is_super_admin && $request->role) {
                $role = \App\Models\Role::where('slug', $request->role)
                    ->where(function($query) use ($request) {
                        $query->where('company_id', $request->company_id)
                              ->orWhere('is_system_role', true);
                    })
                    ->first();

                if ($role) {
                    $user->assignRole($role, $request->company_id);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'company' => $user->company->name ?? 'No Company',
                    'is_super_admin' => $user->is_super_admin,
                    'role' => $request->role ?? 'Super Admin',
                    'created_at' => $user->created_at->format('Y-m-d H:i')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user role/permissions
     */
    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'is_super_admin' => 'required|boolean',
            'company_id' => 'nullable|exists:companies,id'
        ]);

        try {
            $user->update([
                'is_super_admin' => $request->boolean('is_super_admin'),
                'company_id' => $request->company_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User role updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        try {
            // Prevent deletion of the current user
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ], 400);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign regular role to user (not super admin)
     */
    public function assignUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'nullable|in:admin,user'
        ]);

        try {
            // Remove existing roles for this user in their company
            \App\Models\UserRole::where('user_id', $user->id)
                ->where('company_id', $user->company_id)
                ->delete();

            // Assign new role if provided
            if ($request->role) {
                $role = \App\Models\Role::where('slug', $request->role)
                    ->where(function($query) use ($user) {
                        $query->where('company_id', $user->company_id)
                              ->orWhere('is_system_role', true);
                    })
                    ->first();

                if ($role) {
                    $user->assignRole($role, $user->company_id);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'User role updated successfully',
                'role' => $request->role ? ucfirst($request->role) : 'No Role'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user details
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'company_id' => 'exists:companies,id',
            'password' => 'nullable|string|min:8'
        ]);

        try {
            $updateData = [];

            if ($request->has('name')) {
                $updateData['name'] = $request->name;
            }

            if ($request->has('email')) {
                $updateData['email'] = $request->email;
            }

            if ($request->has('company_id')) {
                $updateData['company_id'] = $request->company_id;
            }

            if ($request->has('password') && !empty($request->password)) {
                $updateData['password'] = bcrypt($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new company
     */
    public function createCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:companies',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $company = Company::create([
                'name' => $request->name,
                'status' => $request->status,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Company created successfully',
                'company' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'status' => $company->status,
                    'users_count' => 0,
                    'packages_count' => 0,
                    'recent_packages' => 0,
                    'created_at' => $company->created_at->format('Y-m-d H:i')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create company: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update company details
     */
    public function updateCompany(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'string|max:255|unique:companies,name,' . $company->id,
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $updateData = [];

            if ($request->has('name')) {
                $updateData['name'] = $request->name;
            }

            if ($request->has('description')) {
                $updateData['description'] = $request->description;
            }

            $company->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Company updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update company: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update company status
     */
    public function updateCompanyStatus(Request $request, Company $company)
    {
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $company->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Company status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update company status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * System alerts and monitoring
     */
    public function getSystemAlerts()
    {
        $alerts = [];

        try {
            // Check aging packages
            $agingCount = $this->workflowService->getAgingPackages()->count();
            if ($agingCount > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Aging Packages',
                    'message' => "{$agingCount} packages have been ready for pickup for more than 7 days",
                    'action' => 'View Package Analytics',
                    'priority' => 'high'
                ];
            }

            // Check disk space
            $diskCheck = $this->checkDiskSpace();
            if ($diskCheck['status'] === 'warning') {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Low Disk Space',
                    'message' => $diskCheck['message'],
                    'action' => 'Check Storage Settings',
                    'priority' => 'medium'
                ];
            }

            // Check memory usage
            $memoryCheck = $this->checkMemoryUsage();
            if ($memoryCheck['status'] === 'warning') {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'High Memory Usage',
                    'message' => $memoryCheck['message'],
                    'action' => 'Review System Performance',
                    'priority' => 'medium'
                ];
            }

            // Check for inactive companies with recent activity
            $inactiveCompaniesWithActivity = Company::where('status', 'inactive')
                ->whereHas('packages', function($q) {
                    $q->where('created_at', '>=', now()->subDays(30));
                })
                ->count();

            if ($inactiveCompaniesWithActivity > 0) {
                $alerts[] = [
                    'type' => 'info',
                    'title' => 'Inactive Companies with Activity',
                    'message' => "{$inactiveCompaniesWithActivity} inactive companies have recent package activity",
                    'action' => 'Review Company Status',
                    'priority' => 'low'
                ];
            }

            // Check for unprocessed workflow transitions
            $workflowStats = $this->workflowService->getWorkflowStats();
            if (isset($workflowStats['pending_auto_transitions']) && $workflowStats['pending_auto_transitions'] > 10) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Pending Workflow Transitions',
                    'message' => "{$workflowStats['pending_auto_transitions']} packages need automatic status updates",
                    'action' => 'Process Workflows',
                    'priority' => 'medium'
                ];
            }

            return response()->json(['alerts' => $alerts]);
        } catch (\Exception $e) {
            return response()->json([
                'alerts' => [[
                    'type' => 'error',
                    'title' => 'System Monitoring Error',
                    'message' => 'Unable to check system status: ' . $e->getMessage(),
                    'action' => 'Check System Health',
                    'priority' => 'high'
                ]]
            ]);
        }
    }

    /**
     * Bulk package operations
     */
    public function bulkPackageOperation(Request $request)
    {
        $request->validate([
            'operation' => 'required|in:transition_status,archive,delete',
            'package_ids' => 'required|array|min:1',
            'package_ids.*' => 'integer|exists:packages,id',
            'new_status' => 'required_if:operation,transition_status|in:Incoming,Ready for Pickup,Picked Up,Archived'
        ]);

        try {
            $packageIds = $request->package_ids;
            $operation = $request->operation;
            $results = [];

            switch ($operation) {
                case 'transition_status':
                    $newStatus = $request->new_status;
                    $processed = $this->workflowService->bulkTransition($packageIds, $newStatus);
                    $results = [
                        'success' => true,
                        'message' => "Successfully transitioned {$processed} packages to {$newStatus}",
                        'processed' => $processed
                    ];
                    break;

                case 'archive':
                    $processed = Package::whereIn('id', $packageIds)
                        ->update(['status' => 'Archived']);
                    $results = [
                        'success' => true,
                        'message' => "Successfully archived {$processed} packages",
                        'processed' => $processed
                    ];
                    break;

                case 'delete':
                    $processed = Package::whereIn('id', $packageIds)->delete();
                    $results = [
                        'success' => true,
                        'message' => "Successfully deleted {$processed} packages",
                        'processed' => $processed
                    ];
                    break;
            }

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk operation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * System maintenance tools
     */
    public function maintenance(Request $request)
    {
        $action = $request->input('action');

        try {
            switch ($action) {
                case 'clear_cache':
                    Artisan::call('cache:clear');
                    return response()->json(['success' => true, 'message' => 'Cache cleared successfully']);

                case 'optimize':
                    Artisan::call('optimize');
                    return response()->json(['success' => true, 'message' => 'Application optimized']);

                case 'process_workflow':
                    $processed = $this->workflowService->processAutoTransitions();
                    return response()->json(['success' => true, 'message' => "Processed {$processed} package transitions"]);

                default:
                    return response()->json(['success' => false, 'message' => 'Invalid action']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
