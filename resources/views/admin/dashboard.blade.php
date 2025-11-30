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
        <div id="stats-container" class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8">
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button onclick="loadReports()" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mb-3">
                            <span class="text-white font-bold">📊</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Detailed Reports</h3>
                            <p class="text-xs text-gray-500">Analytics and insights</p>
                        </div>
                    </div>
                </button>

                <button onclick="loadPackageAnalytics()" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mb-3">
                            <span class="text-white font-bold">📦</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Package Analytics</h3>
                            <p class="text-xs text-gray-500">Processing insights</p>
                        </div>
                    </div>
                </button>

                <button onclick="loadUserManagement()" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mb-3">
                            <span class="text-white font-bold">👥</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">User Management</h3>
                            <p class="text-xs text-gray-500">Users and companies</p>
                        </div>
                    </div>
                </button>

                <button onclick="loadSettings()" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mb-3">
                            <span class="text-white font-bold">⚙️</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">System Settings</h3>
                            <p class="text-xs text-gray-500">Configuration</p>
                        </div>
                    </div>
                </button>

                <button onclick="runHealthCheck()" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mb-3">
                            <span class="text-white font-bold">🔍</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">System Health</h3>
                            <p class="text-xs text-gray-500">Diagnostics</p>
                        </div>
                    </div>
                </button>

                <button onclick="loadMaintenance()" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mb-3">
                            <span class="text-white font-bold">🔧</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Maintenance</h3>
                            <p class="text-xs text-gray-500">System tools</p>
                        </div>
                    </div>
                </button>

                <button onclick="loadBulkOperations()" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center mb-3">
                            <span class="text-white font-bold">⚡</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Bulk Operations</h3>
                            <p class="text-xs text-gray-500">Package management</p>
                        </div>
                    </div>
                </button>

                <button onclick="loadSystemAlerts()" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-pink-500 rounded-lg flex items-center justify-center mb-3">
                            <span class="text-white font-bold">🚨</span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">System Alerts</h3>
                            <p class="text-xs text-gray-500">Monitoring</p>
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
    loadSystemAlerts(); // Load alerts on page load
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
                        <p class="text-sm font-medium text-gray-500">Ready Packages</p>
                        <p class="text-2xl font-semibold text-gray-900">${data.stats.ready_packages || 0}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">⏰</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Aging Packages</p>
                        <p class="text-2xl font-semibold text-gray-900 ${data.stats.aging_packages > 0 ? 'text-red-600' : ''}">${data.stats.aging_packages || 0}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">🚀</span>
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
            <div class="col-span-6">
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
                <h3 class="text-lg font-semibold text-gray-900 mb-6">System Reports & Analytics</h3>

                <!-- Performance Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900">Today's Packages</h4>
                        <p class="text-2xl font-bold text-blue-700">${data.performanceMetrics.total_packages_today}</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-medium text-green-900">Picked Up Today</h4>
                        <p class="text-2xl font-bold text-green-700">${data.performanceMetrics.packages_picked_up_today}</p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="font-medium text-yellow-900">Aging Packages</h4>
                        <p class="text-2xl font-bold text-yellow-700">${data.performanceMetrics.aging_packages}</p>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h4 class="font-medium text-purple-900">Avg Processing (hrs)</h4>
                        <p class="text-2xl font-bold text-purple-700">${Math.round(data.performanceMetrics.average_processing_time * 10) / 10}</p>
                    </div>
                </div>

                <!-- Company Performance -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Company Performance</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-2">Company</th>
                                        <th class="text-right py-2">Total Packages</th>
                                        <th class="text-right py-2">Recent (30d)</th>
                                        <th class="text-right py-2">Avg Processing</th>
                                        <th class="text-center py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.companyStats.map(company => `
                                        <tr class="border-b border-gray-100">
                                            <td class="py-2 font-medium">${company.name}</td>
                                            <td class="text-right py-2">${company.packages_count}</td>
                                            <td class="text-right py-2">${company.recent_packages}</td>
                                            <td class="text-right py-2">${company.avg_processing_time}h</td>
                                            <td class="text-center py-2">
                                                <span class="px-2 py-1 text-xs rounded ${company.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                                    ${company.status}
                                                </span>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Recent Activity (Last 24 Hours)</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            ${data.recentActivity.map(package => `
                                <div class="flex items-center justify-between p-2 bg-white rounded border">
                                    <div>
                                        <span class="font-medium">${package.tracking_number}</span>
                                        <span class="text-sm text-gray-600 ml-2">${package.company}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">${package.status}</span>
                                        <span class="text-sm text-gray-500">${package.created_at}</span>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>

                <!-- Status Distribution -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Package Status Distribution</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        ${Object.entries(data.statusDistribution).map(([status, count]) => `
                            <div class="bg-white border rounded-lg p-4">
                                <h5 class="text-sm font-medium text-gray-600">${status}</h5>
                                <p class="text-2xl font-bold text-gray-900">${count}</p>
                            </div>
                        `).join('')}
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

        let settingsHtml = '<div class="p-6"><h3 class="text-lg font-semibold text-gray-900 mb-6">System Configuration</h3>';

        for (const [category, categorySettings] of Object.entries(data.settings)) {
            settingsHtml += `
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-800 mb-3 capitalize">${category} Settings</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            `;

            for (const [key, value] of Object.entries(categorySettings)) {
                const displayValue = typeof value === 'object' ? JSON.stringify(value) : value;
                const isHealthy = key.includes('status') ? (value === 'healthy' ? 'text-green-600' : 'text-red-600') : 'text-gray-700';

                settingsHtml += `
                    <div class="bg-white rounded border p-3">
                        <div class="text-sm font-medium text-gray-600">${key.replace(/_/g, ' ').toUpperCase()}</div>
                        <div class="text-lg ${isHealthy}">${displayValue}</div>
                    </div>
                `;
            }

            settingsHtml += `
                        </div>
                    </div>
                </div>
            `;
        }

        settingsHtml += '</div>';
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

        let healthHtml = '<div class="p-6"><h3 class="text-lg font-semibold text-gray-900 mb-6">System Health Check</h3>';

        // Overall status banner
        const overallStatus = data.overall_status || 'unknown';
        const bannerColor = overallStatus === 'healthy' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-yellow-50 border-yellow-200 text-yellow-800';

        healthHtml += `
            <div class="mb-6 ${bannerColor} border rounded-lg p-4">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">${overallStatus === 'healthy' ? '✅' : '⚠️'}</span>
                    <div>
                        <h4 class="font-medium">Overall System Status: ${overallStatus.toUpperCase()}</h4>
                        <p class="text-sm">System health monitoring results</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        `;

        // Remove overall_status from individual checks
        const { overall_status, ...healthChecks } = data;

        for (const [key, check] of Object.entries(healthChecks)) {
            const status = check.status || check;
            const message = check.message || (typeof check === 'string' ? check : 'Status check completed');

            const isHealthy = status === 'healthy' || status === 'connected' || status === true;
            const statusColor = isHealthy ? 'text-green-600' : (status === 'warning' ? 'text-yellow-600' : 'text-red-600');
            const borderColor = isHealthy ? 'border-green-200' : (status === 'warning' ? 'border-yellow-200' : 'border-red-200');
            const bgColor = isHealthy ? 'bg-green-50' : (status === 'warning' ? 'bg-yellow-50' : 'bg-red-50');
            const icon = isHealthy ? '✅' : (status === 'warning' ? '⚠️' : '❌');

            healthHtml += `
                <div class="border ${borderColor} ${bgColor} rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900">${key.replace(/_/g, ' ').toUpperCase()}</h4>
                        <span class="text-xl">${icon}</span>
                    </div>
                    <p class="${statusColor} text-sm font-medium mb-1">${typeof status === 'string' ? status : (status ? 'OK' : 'Failed')}</p>
                    <p class="text-xs text-gray-600">${message}</p>
                    ${check.details ? `<div class="mt-2 text-xs text-gray-500">${JSON.stringify(check.details)}</div>` : ''}
                </div>
            `;
        }

        healthHtml += '</div></div>';
        contentArea.innerHTML = healthHtml;
    } catch (error) {
        contentArea.innerHTML = `<div class="p-6"><div class="text-red-500">Error running health check: ${error.message}</div></div>`;
    }
}

async function loadPackageAnalytics() {
    const contentArea = document.getElementById('content-area');
    contentArea.innerHTML = '<div class="p-6"><div class="text-center py-4">Loading package analytics...</div></div>';

    try {
        const response = await fetch('{{ route("admin.package-analytics") }}', {
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
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Package Analytics & Performance</h3>

                <!-- Status Distribution -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Package Status Distribution</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        ${Object.entries(data.statusDistribution).map(([status, count]) => `
                            <div class="bg-white border rounded-lg p-4">
                                <h5 class="text-sm font-medium text-gray-600">${status}</h5>
                                <p class="text-2xl font-bold text-blue-600">${count}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>

                <!-- Processing Times -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Processing Time Analysis (Top 10 Longest)</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-2">Tracking Number</th>
                                        <th class="text-right py-2">Processing Time (Hours)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.processingTimes.slice(0, 10).map(pkg => `
                                        <tr class="border-b border-gray-100">
                                            <td class="py-2 font-medium">${pkg.tracking_number}</td>
                                            <td class="text-right py-2 ${pkg.processing_hours > 48 ? 'text-red-600' : pkg.processing_hours > 24 ? 'text-yellow-600' : 'text-green-600'}">${pkg.processing_hours}h</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Aging Packages -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Aging Packages (Ready for Pickup > 7 days)</h4>
                    <div class="bg-yellow-50 rounded-lg p-4">
                        ${data.agingPackages.length > 0 ? `
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-yellow-200">
                                            <th class="text-left py-2">Tracking Number</th>
                                            <th class="text-left py-2">Company</th>
                                            <th class="text-right py-2">Ready Since</th>
                                            <th class="text-right py-2">Age (Days)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.agingPackages.map(pkg => `
                                            <tr class="border-b border-yellow-100">
                                                <td class="py-2 font-medium">${pkg.tracking_number}</td>
                                                <td class="py-2">${pkg.company}</td>
                                                <td class="text-right py-2">${pkg.ready_at || 'N/A'}</td>
                                                <td class="text-right py-2 text-red-600 font-bold">${pkg.age_days || 'N/A'}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        ` : '<p class="text-green-600 text-center py-4">✅ No aging packages found!</p>'}
                    </div>
                </div>

                <!-- Daily Trends -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Daily Package Trends (Last 30 Days)</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 max-h-64 overflow-y-auto">
                            ${data.dailyTrends.map(trend => `
                                <div class="bg-white rounded border p-2">
                                    <div class="text-xs text-gray-500">${trend.date}</div>
                                    <div class="text-lg font-bold text-blue-600">${trend.count}</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `;
    } catch (error) {
        contentArea.innerHTML = `<div class="p-6"><div class="text-red-500">Error loading package analytics: ${error.message}</div></div>`;
    }
}

async function loadUserManagement() {
    const contentArea = document.getElementById('content-area');
    contentArea.innerHTML = '<div class="p-6"><div class="text-center py-4">Loading user management...</div></div>';

    try {
        const [usersResponse, companiesResponse] = await Promise.all([
            fetch('{{ route("admin.users") }}', {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }),
            fetch('{{ route("admin.companies") }}', {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
        ]);

        if (!usersResponse.ok || !companiesResponse.ok) {
            throw new Error('Failed to fetch user management data');
        }

        const usersData = await usersResponse.json();
        const companiesData = await companiesResponse.json();

        contentArea.innerHTML = `
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">User & Company Management</h3>

                <!-- Action Buttons -->
                <div class="mb-6 flex flex-wrap gap-3">
                    <button onclick="showCreateUserForm()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                        + Create User
                    </button>
                    <button onclick="showCreateCompanyForm()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors">
                        + Create Company
                    </button>
                    <button onclick="loadUserManagement()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
                        🔄 Refresh
                    </button>
                </div>

                <!-- Create User Form (Hidden by default) -->
                <div id="create-user-form" class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4" style="display: none;">
                    <h4 class="font-medium text-blue-900 mb-3">Create New User</h4>
                    <form onsubmit="createUser(event)">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" name="password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                                <select name="company_id" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Company</option>
                                    ${companiesData.companies.filter(c => c.status === 'active').map(company => `
                                        <option value="${company.id}">${company.name}</option>
                                    `).join('')}
                                </select>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_super_admin" id="is_super_admin" class="mr-2">
                                <label for="is_super_admin" class="text-sm font-medium text-gray-700">Super Admin</label>
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create User</button>
                            <button type="button" onclick="hideCreateUserForm()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Create Company Form (Hidden by default) -->
                <div id="create-company-form" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4" style="display: none;">
                    <h4 class="font-medium text-green-900 mb-3">Create New Company</h4>
                    <form onsubmit="createCompany(event)">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-green-500 focus:border-green-500">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-green-500 focus:border-green-500"></textarea>
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Create Company</button>
                            <button type="button" onclick="hideCreateCompanyForm()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Companies -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-900 mb-3">Companies Overview</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-2">Company Name</th>
                                        <th class="text-center py-2">Status</th>
                                        <th class="text-right py-2">Users</th>
                                        <th class="text-right py-2">Total Packages</th>
                                        <th class="text-right py-2">Recent (7d)</th>
                                        <th class="text-right py-2">Created</th>
                                        <th class="text-center py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${companiesData.companies.map(company => `
                                        <tr class="border-b border-gray-100">
                                            <td class="py-2 font-medium">${company.name}</td>
                                            <td class="text-center py-2">
                                                <select onchange="updateCompanyStatus(${company.id}, this.value)" class="px-2 py-1 text-xs rounded border ${company.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                                    <option value="active" ${company.status === 'active' ? 'selected' : ''}>Active</option>
                                                    <option value="inactive" ${company.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                                </select>
                                            </td>
                                            <td class="text-right py-2">${company.users_count}</td>
                                            <td class="text-right py-2">${company.packages_count}</td>
                                            <td class="text-right py-2">${company.recent_packages}</td>
                                            <td class="text-right py-2 text-xs text-gray-500">${company.created_at}</td>
                                            <td class="text-center py-2">
                                                <button class="text-blue-600 hover:text-blue-800 text-xs">Edit</button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Users -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Users Overview</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-2">Name</th>
                                        <th class="text-left py-2">Email</th>
                                        <th class="text-left py-2">Company</th>
                                        <th class="text-center py-2">Admin</th>
                                        <th class="text-right py-2">Last Login</th>
                                        <th class="text-right py-2">Created</th>
                                        <th class="text-center py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${usersData.users.map(user => `
                                        <tr class="border-b border-gray-100">
                                            <td class="py-2 font-medium">${user.name}</td>
                                            <td class="py-2">${user.email}</td>
                                            <td class="py-2">${user.company}</td>
                                            <td class="text-center py-2">
                                                <input type="checkbox" ${user.is_super_admin ? 'checked' : ''} onchange="updateUserRole(${user.id}, this.checked)" class="rounded">
                                            </td>
                                            <td class="text-right py-2 text-xs text-gray-500">${user.last_login}</td>
                                            <td class="text-right py-2 text-xs text-gray-500">${user.created_at}</td>
                                            <td class="text-center py-2">
                                                <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-800 text-xs ml-2">Delete</button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } catch (error) {
        contentArea.innerHTML = `<div class="p-6"><div class="text-red-500">Error loading user management: ${error.message}</div></div>`;
    }
}

async function loadMaintenance() {
    const contentArea = document.getElementById('content-area');

    contentArea.innerHTML = `
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">System Maintenance Tools</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Cache Management -->
                <div class="bg-white border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">Cache Management</h4>
                    <p class="text-sm text-gray-600 mb-4">Clear application cache to resolve performance issues</p>
                    <button onclick="runMaintenance('clear_cache')" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                        Clear Cache
                    </button>
                </div>

                <!-- System Optimization -->
                <div class="bg-white border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">System Optimization</h4>
                    <p class="text-sm text-gray-600 mb-4">Optimize application performance and caching</p>
                    <button onclick="runMaintenance('optimize')" class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors">
                        Optimize System
                    </button>
                </div>

                <!-- Workflow Processing -->
                <div class="bg-white border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">Process Workflows</h4>
                    <p class="text-sm text-gray-600 mb-4">Manually trigger workflow auto-transitions</p>
                    <button onclick="runMaintenance('process_workflow')" class="w-full bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 transition-colors">
                        Process Transitions
                    </button>
                </div>
            </div>

            <div id="maintenance-results" class="mt-6"></div>
        </div>
    `;
}

async function runMaintenance(action) {
    const resultsDiv = document.getElementById('maintenance-results');
    resultsDiv.innerHTML = '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-blue-800">Processing...</div>';

    try {
        const response = await fetch('{{ route("admin.maintenance") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ action })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            resultsDiv.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">
                    <div class="flex items-center">
                        <span class="text-xl mr-2">✅</span>
                        <span>${data.message}</span>
                    </div>
                </div>
            `;
        } else {
            resultsDiv.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">
                    <div class="flex items-center">
                        <span class="text-xl mr-2">❌</span>
                        <span>${data.message}</span>
                    </div>
                </div>
            `;
        }

        // Clear results after 5 seconds
        setTimeout(() => {
            resultsDiv.innerHTML = '';
        }, 5000);

    } catch (error) {
        resultsDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">
                <div class="flex items-center">
                    <span class="text-xl mr-2">❌</span>
                    <span>Error: ${error.message}</span>
                </div>
            </div>
        `;
    }
}

// User Management Functions
function showCreateUserForm() {
    document.getElementById('create-user-form').style.display = 'block';
    document.getElementById('create-company-form').style.display = 'none';
}

function hideCreateUserForm() {
    document.getElementById('create-user-form').style.display = 'none';
}

function showCreateCompanyForm() {
    document.getElementById('create-company-form').style.display = 'block';
    document.getElementById('create-user-form').style.display = 'none';
}

function hideCreateCompanyForm() {
    document.getElementById('create-company-form').style.display = 'none';
}

async function createUser(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    try {
        const response = await fetch('{{ route("admin.create-user") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showToast('User created successfully!', 'success');
            hideCreateUserForm();
            form.reset();
            loadUserManagement();
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    } catch (error) {
        showToast('Error creating user: ' + error.message, 'error');
    }
}

async function createCompany(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    try {
        const response = await fetch('{{ route("admin.create-company") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showToast('Company created successfully!', 'success');
            hideCreateCompanyForm();
            form.reset();
            loadUserManagement();
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    } catch (error) {
        showToast('Error creating company: ' + error.message, 'error');
    }
}

async function updateUserRole(userId, isSuperAdmin) {
    try {
        const response = await fetch(`{{ url('admin/users') }}/${userId}/role`, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ is_super_admin: isSuperAdmin })
        });

        const data = await response.json();

        if (data.success) {
            showToast('User role updated successfully!', 'success');
        } else {
            showToast('Error: ' + data.message, 'error');
            // Revert checkbox
            event.target.checked = !isSuperAdmin;
        }
    } catch (error) {
        showToast('Error updating user role: ' + error.message, 'error');
        // Revert checkbox
        event.target.checked = !isSuperAdmin;
    }
}

async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`{{ url('admin/users') }}/${userId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (data.success) {
            showToast('User deleted successfully!', 'success');
            loadUserManagement();
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    } catch (error) {
        showToast('Error deleting user: ' + error.message, 'error');
    }
}

async function updateCompanyStatus(companyId, status) {
    try {
        const response = await fetch(`{{ url('admin/companies') }}/${companyId}/status`, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        });

        const data = await response.json();

        if (data.success) {
            showToast('Company status updated successfully!', 'success');
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    } catch (error) {
        showToast('Error updating company status: ' + error.message, 'error');
    }
}

// System Alerts
async function loadSystemAlerts() {
    try {
        const response = await fetch('{{ route("admin.system-alerts") }}', {
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
        displaySystemAlerts(data.alerts);
    } catch (error) {
        console.error('Error loading system alerts:', error);
    }
}

function displaySystemAlerts(alerts) {
    if (alerts.length === 0) return;

    const alertContainer = document.createElement('div');
    alertContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
    alertContainer.id = 'system-alerts';

    alerts.forEach((alert, index) => {
        const alertElement = document.createElement('div');
        const alertClass = alert.type === 'error' ? 'bg-red-50 border-red-200 text-red-800' :
                          alert.type === 'warning' ? 'bg-yellow-50 border-yellow-200 text-yellow-800' :
                          'bg-blue-50 border-blue-200 text-blue-800';

        alertElement.className = `${alertClass} border rounded-lg p-3 shadow-lg max-w-sm`;
        alertElement.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <span class="text-lg">${alert.type === 'error' ? '❌' : alert.type === 'warning' ? '⚠️' : 'ℹ️'}</span>
                </div>
                <div class="ml-3 flex-1">
                    <h4 class="text-sm font-medium">${alert.title}</h4>
                    <p class="text-xs mt-1">${alert.message}</p>
                    ${alert.action ? `<button onclick="handleAlertAction('${alert.action}')" class="text-xs underline mt-1">${alert.action}</button>` : ''}
                </div>
                <button onclick="dismissAlert(this)" class="ml-2 text-gray-400 hover:text-gray-600">×</button>
            </div>
        `;

        alertContainer.appendChild(alertElement);

        // Auto-dismiss after 10 seconds for low priority alerts
        if (alert.priority === 'low') {
            setTimeout(() => {
                if (alertElement.parentNode) {
                    alertElement.remove();
                }
            }, 10000);
        }
    });

    // Remove existing alerts
    const existingAlerts = document.getElementById('system-alerts');
    if (existingAlerts) {
        existingAlerts.remove();
    }

    document.body.appendChild(alertContainer);
}

function dismissAlert(button) {
    const alert = button.closest('div').parentElement;
    alert.remove();
}

function handleAlertAction(action) {
    switch (action) {
        case 'View Package Analytics':
            loadPackageAnalytics();
            break;
        case 'Check Storage Settings':
            loadSettings();
            break;
        case 'Review System Performance':
            runHealthCheck();
            break;
        case 'Review Company Status':
            loadUserManagement();
            break;
        case 'Process Workflows':
            runMaintenance('process_workflow');
            break;
        case 'Check System Health':
            runHealthCheck();
            break;
        default:
            console.log('Unknown alert action:', action);
    }
}

// Bulk Operations
async function loadBulkOperations() {
    const contentArea = document.getElementById('content-area');

    contentArea.innerHTML = `
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Bulk Package Operations</h3>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <span class="text-yellow-600 text-lg mr-2">⚠️</span>
                    <div>
                        <h4 class="text-yellow-900 font-medium">Bulk Operations Warning</h4>
                        <p class="text-yellow-800 text-sm">These operations affect multiple packages at once. Please use with caution.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Status Transition -->
                <div class="bg-white border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">Bulk Status Transition</h4>
                    <p class="text-sm text-gray-600 mb-4">Update status for multiple packages</p>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Package IDs (comma-separated)</label>
                        <textarea id="bulk-transition-ids" rows="3" placeholder="1,2,3,4,5" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Status</label>
                        <select id="bulk-transition-status" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Status</option>
                            <option value="Incoming">Incoming</option>
                            <option value="Ready for Pickup">Ready for Pickup</option>
                            <option value="Picked Up">Picked Up</option>
                            <option value="Archived">Archived</option>
                        </select>
                    </div>

                    <button onclick="bulkTransitionStatus()" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                        Update Status
                    </button>
                </div>

                <!-- Archive Packages -->
                <div class="bg-white border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">Bulk Archive</h4>
                    <p class="text-sm text-gray-600 mb-4">Archive multiple packages</p>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Package IDs (comma-separated)</label>
                        <textarea id="bulk-archive-ids" rows="3" placeholder="1,2,3,4,5" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-green-500 focus:border-green-500"></textarea>
                    </div>

                    <button onclick="bulkArchivePackages()" class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors">
                        Archive Packages
                    </button>
                </div>

                <!-- Delete Packages -->
                <div class="bg-white border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">Bulk Delete</h4>
                    <p class="text-sm text-gray-600 mb-4">Permanently delete multiple packages</p>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Package IDs (comma-separated)</label>
                        <textarea id="bulk-delete-ids" rows="3" placeholder="1,2,3,4,5" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-red-500 focus:border-red-500"></textarea>
                    </div>

                    <button onclick="bulkDeletePackages()" class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
                        Delete Packages
                    </button>
                </div>
            </div>

            <div id="bulk-operation-results" class="mt-6"></div>
        </div>
    `;
}

async function bulkTransitionStatus() {
    const idsText = document.getElementById('bulk-transition-ids').value.trim();
    const newStatus = document.getElementById('bulk-transition-status').value;

    if (!idsText || !newStatus) {
        showToast('Please provide package IDs and select a status', 'error');
        return;
    }

    const packageIds = idsText.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));

    if (packageIds.length === 0) {
        showToast('Please provide valid package IDs', 'error');
        return;
    }

    await performBulkOperation('transition_status', packageIds, { new_status: newStatus });
}

async function bulkArchivePackages() {
    const idsText = document.getElementById('bulk-archive-ids').value.trim();

    if (!idsText) {
        showToast('Please provide package IDs', 'error');
        return;
    }

    const packageIds = idsText.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));

    if (packageIds.length === 0) {
        showToast('Please provide valid package IDs', 'error');
        return;
    }

    if (!confirm(`Are you sure you want to archive ${packageIds.length} packages?`)) {
        return;
    }

    await performBulkOperation('archive', packageIds);
}

async function bulkDeletePackages() {
    const idsText = document.getElementById('bulk-delete-ids').value.trim();

    if (!idsText) {
        showToast('Please provide package IDs', 'error');
        return;
    }

    const packageIds = idsText.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));

    if (packageIds.length === 0) {
        showToast('Please provide valid package IDs', 'error');
        return;
    }

    if (!confirm(`Are you sure you want to PERMANENTLY DELETE ${packageIds.length} packages? This action cannot be undone.`)) {
        return;
    }

    await performBulkOperation('delete', packageIds);
}

async function performBulkOperation(operation, packageIds, extraData = {}) {
    const resultsDiv = document.getElementById('bulk-operation-results');
    resultsDiv.innerHTML = '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-blue-800">Processing...</div>';

    try {
        const requestData = {
            operation: operation,
            package_ids: packageIds,
            ...extraData
        };

        const response = await fetch('{{ route("admin.bulk-package-operation") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(requestData)
        });

        const data = await response.json();

        if (data.success) {
            resultsDiv.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">
                    <div class="flex items-center">
                        <span class="text-xl mr-2">✅</span>
                        <span>${data.message}</span>
                    </div>
                </div>
            `;
            showToast(data.message, 'success');
        } else {
            resultsDiv.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">
                    <div class="flex items-center">
                        <span class="text-xl mr-2">❌</span>
                        <span>${data.message}</span>
                    </div>
                </div>
            `;
            showToast('Error: ' + data.message, 'error');
        }

        // Clear results after 5 seconds
        setTimeout(() => {
            resultsDiv.innerHTML = '';
        }, 5000);

    } catch (error) {
        resultsDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">
                <div class="flex items-center">
                    <span class="text-xl mr-2">❌</span>
                    <span>Error: ${error.message}</span>
                </div>
            </div>
        `;
        showToast('Error: ' + error.message, 'error');
    }
}

// Toast notification system
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');

    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';

    toast.className = `${bgColor} text-white px-6 py-3 rounded shadow-lg mb-2 transform translate-x-full transition-transform duration-300`;
    toast.innerHTML = `
        <div class="flex items-center justify-between">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">×</button>
        </div>
    `;

    toastContainer.appendChild(toast);

    // Trigger animation
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 300);
        }
    }, 5000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'fixed top-4 right-4 z-50 space-y-2';
    document.body.appendChild(container);
    return container;
}
</script>
@endsection
