@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600">System administration and management</p>
        </div>

        <!-- Stats Loading -->
        <div id="stats-container" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="animate-pulse">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-8 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>
            <div class="animate-pulse">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-8 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>
            <div class="animate-pulse">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-8 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>
            <div class="animate-pulse">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-8 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Administrative Tools</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button onclick="loadReports()" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mb-3">
                            <span class="text-white font-bold">📊</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">View Reports</h3>
                            <p class="text-xs text-gray-500">System analytics and reports</p>
                        </div>
                    </div>
                </button>

                <button onclick="loadSettings()" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mb-3">
                            <span class="text-white font-bold">⚙️</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">System Settings</h3>
                            <p class="text-xs text-gray-500">Configure system preferences</p>
                        </div>
                    </div>
                </button>

                <button onclick="runHealthCheck()" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mb-3">
                            <span class="text-white font-bold">🔍</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Health Check</h3>
                            <p class="text-xs text-gray-500">System status and diagnostics</p>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div id="content-area" class="bg-white rounded-lg shadow">
            <div class="p-6">
                <div class="text-center py-12">
                    <div class="text-gray-500 text-lg">Select an administrative tool above to get started</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardStats();
});

async function loadDashboardStats() {
    try {
        const response = await fetch('{{ route("admin.dashboard") }}', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        const statsHtml = `
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">🏢</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Companies</p>
                        <p class="text-2xl font-semibold text-gray-900">${data.stats.company_stats?.total_companies || 0}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">👥</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Users</p>
                        <p class="text-2xl font-semibold text-gray-900">${data.stats.company_stats?.total_users || 0}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">📦</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Packages</p>
                        <p class="text-2xl font-semibold text-gray-900">${data.stats.total_packages || 0}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">📈</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Recent Activity</p>
                        <p class="text-2xl font-semibold text-gray-900">${data.stats.recent_packages || 0}</p>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('stats-container').innerHTML = statsHtml;
    } catch (error) {
        console.error('Error loading dashboard stats:', error);
        // Show error message in UI
        document.getElementById('stats-container').innerHTML = `
            <div class="col-span-4">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="text-red-800 text-sm">
                        <strong>Error loading stats:</strong> ${error.message}
                        <br><span class="text-xs">Check browser console for details.</span>
                    </div>
                </div>
            </div>
        `;
    }
}

async function loadReports() {
    const contentArea = document.getElementById('content-area');
    contentArea.innerHTML = '<div class="p-6"><div class="text-center py-4">Loading reports...</div></div>';

    try {
        const response = await fetch('{{ route("admin.reports") }}', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        contentArea.innerHTML = `
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">System Reports</h3>
                <div class="space-y-4">
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900">Company Statistics</h4>
                        <p class="text-sm text-gray-600">Companies: ${data.companyStats?.length || 0}</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900">Package Trends</h4>
                        <p class="text-sm text-gray-600">Trend data: ${data.packageTrends?.length || 0} entries</p>
                    </div>
                </div>
            </div>
        `;
    } catch (error) {
        contentArea.innerHTML = `<div class="p-6"><div class="text-red-500">Error loading reports: ${error.message}</div></div>`;
    }
}

async function loadSettings() {
    const contentArea = document.getElementById('content-area');
    contentArea.innerHTML = '<div class="p-6"><div class="text-center py-4">Loading settings...</div></div>';

    try {
        const response = await fetch('{{ route("admin.settings") }}', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        let settingsHtml = '<div class="p-6"><h3 class="text-lg font-semibold text-gray-900 mb-4">System Settings</h3><div class="space-y-4">';

        for (const [key, value] of Object.entries(data.settings)) {
            settingsHtml += `
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900">${key.replace(/_/g, ' ').toUpperCase()}</h4>
                    <p class="text-sm text-gray-600">${typeof value === 'object' ? JSON.stringify(value) : value}</p>
                </div>
            `;
        }

        settingsHtml += '</div></div>';
        contentArea.innerHTML = settingsHtml;
    } catch (error) {
        contentArea.innerHTML = `<div class="p-6"><div class="text-red-500">Error loading settings: ${error.message}</div></div>`;
    }
}

async function runHealthCheck() {
    const contentArea = document.getElementById('content-area');
    contentArea.innerHTML = '<div class="p-6"><div class="text-center py-4">Running health check...</div></div>';

    try {
        const response = await fetch('{{ route("admin.health-check") }}', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        let healthHtml = '<div class="p-6"><h3 class="text-lg font-semibold text-gray-900 mb-4">System Health Check</h3><div class="space-y-4">';

        for (const [key, status] of Object.entries(data)) {
            const isHealthy = status === 'healthy' || status === 'connected' || status === true;
            const statusColor = isHealthy ? 'text-green-600' : 'text-red-600';
            const icon = isHealthy ? '✅' : '❌';

            healthHtml += `
                <div class="border rounded-lg p-4 flex items-center justify-between">
                    <h4 class="font-medium text-gray-900">${key.replace(/_/g, ' ').toUpperCase()}</h4>
                    <span class="${statusColor} flex items-center">
                        ${icon} ${typeof status === 'string' ? status : (status ? 'OK' : 'Failed')}
                    </span>
                </div>
            `;
        }

        healthHtml += '</div></div>';
        contentArea.innerHTML = healthHtml;
    } catch (error) {
        contentArea.innerHTML = `<div class="p-6"><div class="text-red-500">Error running health check: ${error.message}</div></div>`;
    }
}
</script>
@endsection
