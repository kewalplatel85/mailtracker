<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class AdminController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
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
        $pendingPackages = Package::where('status', 'pending')->count();
        $shippedPackages = Package::where('status', 'shipped')->count();
        $deliveredPackages = Package::where('status', 'delivered')->count();

        return [
            'company_stats' => [
                'total_companies' => $totalCompanies,
                'total_users' => $totalUsers,
            ],
            'total_packages' => $totalPackages,
            'pending_packages' => $pendingPackages,
            'shipped_packages' => $shippedPackages,
            'delivered_packages' => $deliveredPackages,
            'recent_packages' => $recentPackages,
        ];
    }

    /**
     * Show system reports
     */
    public function reports()
    {
        // Company performance metrics
        $companyStats = Company::withCount(['users', 'packages'])->get()->map(function($company) {
            return [
                'name' => $company->name,
                'users_count' => $company->users_count,
                'packages_count' => $company->packages_count,
                'status' => $company->status,
                'created_at' => $company->created_at
            ];
        });

        // Package processing trends (last 30 days)
        $packageTrends = Package::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports', compact('companyStats', 'packageTrends'));
    }

    /**
     * System settings management
     */
    public function settings()
    {
        // Get system configuration
        $settings = [
            'app_name' => config('app.name'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'database_connection' => config('database.default'),
            'mail_driver' => config('mail.driver'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
        ];

        return view('admin.settings', compact('settings'));
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
        ];

        return response()->json($health);
    }

    private function checkDatabaseHealth()
    {
        try {
            \DB::connection()->getPdo();
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
            \Storage::put($testFile, 'test');
            $content = \Storage::get($testFile);
            \Storage::delete($testFile);

            return ['status' => $content === 'test' ? 'healthy' : 'unhealthy', 'message' => 'Storage system operational'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Storage system error: ' . $e->getMessage()];
        }
    }

    private function checkMailHealth()
    {
        try {
            // Just check if mail configuration is properly set
            $driver = config('mail.driver');
            return ['status' => 'healthy', 'message' => "Mail driver: $driver"];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Mail configuration error: ' . $e->getMessage()];
        }
    }
}
