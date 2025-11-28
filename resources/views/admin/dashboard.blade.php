@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-4">
    <div class="max-w-full mx-auto px-6 sm:px-8 lg:px-12">
        <!-- Admin Header -->
        <div class="mb-6">
            <div class="bg-gradient-to-r from-red-600 to-red-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">System Administration Dashboard</h1>
                        <p class="text-red-100 mt-1">Manage companies, users, and system-wide settings</p>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-crown text-yellow-300"></i>
                            <span class="text-lg font-medium">Super Admin</span>
                        </div>
                        <p class="text-red-100 text-sm">{{ Auth::user()->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Active Companies</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['company_stats']['total_companies'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['company_stats']['total_users'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Packages</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_packages'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Recent Activity</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['recent_packages'] ?? 0 }}</p>
                        <p class="text-xs text-gray-400">Last 7 days</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Quick Actions -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Administrative Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('companies.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow group">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                            <i class="fas fa-building text-white text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Company Management</h3>
                        <p class="text-sm text-gray-500">Create, edit, and manage companies</p>
                    </div>
                </a>

                <a href="{{ route('users.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow group">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                            <i class="fas fa-users-cog text-white text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">User Management</h3>
                        <p class="text-sm text-gray-500">Manage users and role assignments</p>
                    </div>
                </a>

                <a href="#" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow group">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                            <i class="fas fa-cogs text-white text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">System Settings</h3>
                        <p class="text-sm text-gray-500">Configure system-wide settings</p>
                    </div>
                </a>

                <a href="#" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow group">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                            <i class="fas fa-chart-bar text-white text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">System Reports</h3>
                        <p class="text-sm text-gray-500">View comprehensive system reports</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activity and System Status -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Companies -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Companies</h3>
                    <p class="text-sm text-gray-500">Latest company registrations</p>
                </div>
                <div class="p-6">
                    @if($recentCompanies->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentCompanies as $company)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold">{{ substr($company->name, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">{{ $company->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $company->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="badge bg-{{ $company->status == 'active' ? 'success' : 'warning' }} text-xs px-2 py-1 rounded-full">
                                        {{ ucfirst($company->status) }}
                                    </span>
                                    <a href="{{ route('companies.show', $company) }}" class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-building fa-3x text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No companies found</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">System Status</h3>
                    <p class="text-sm text-gray-500">Current system health indicators</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="font-medium text-gray-900">Database Connection</span>
                            </div>
                            <span class="text-green-600 font-medium">Healthy</span>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="font-medium text-gray-900">Application Status</span>
                            </div>
                            <span class="text-green-600 font-medium">Running</span>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="font-medium text-gray-900">Cache System</span>
                            </div>
                            <span class="text-blue-600 font-medium">Active</span>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                <span class="font-medium text-gray-900">Background Jobs</span>
                            </div>
                            <span class="text-yellow-600 font-medium">Monitoring</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Summary -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Platform Overview</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600 mb-1">{{ $stats['company_stats']['total_companies'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Active Companies</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600 mb-1">{{ $stats['total_packages'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Total Packages Processed</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600 mb-1">{{ $stats['company_stats']['total_users'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Registered Users</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
