@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">User: {{ $user->name }}</h1>
                <div>
                    @can('users.edit')
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                    @endcan
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- User Info Card -->
                <div class="col-xl-4 col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">User Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Name:</strong></div>
                                <div class="col-sm-8">{{ $user->name }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Email:</strong></div>
                                <div class="col-sm-8">
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                    @if($user->email_verified_at)
                                        <small class="text-success d-block">
                                            <i class="fas fa-check-circle"></i> Verified on {{ $user->email_verified_at->format('M d, Y') }}
                                        </small>
                                    @else
                                        <small class="text-warning d-block">
                                            <i class="fas fa-exclamation-circle"></i> Email not verified
                                        </small>
                                    @endif
                                </div>
                            </div>

                            @if($user->company)
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Company:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge bg-info">{{ $user->company->name }}</span>
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Role:</strong></div>
                                <div class="col-sm-8">
                                    @if($user->is_super_admin)
                                        <span class="badge bg-danger">Super Admin</span>
                                    @else
                                        @php
                                            $companyId = Auth::user()->is_super_admin 
                                                ? ($user->company_id ?? session('selected_company_id')) 
                                                : Auth::user()->company_id;
                                            $userRole = $user->userRoles()->where('company_id', $companyId)->with('role')->first();
                                        @endphp
                                        @if($userRole && $userRole->role)
                                            <span class="badge bg-primary">{{ $userRole->role->name }}</span>
                                            <br><small class="text-muted">{{ $userRole->role->description }}</small>
                                        @else
                                            <span class="badge bg-secondary">No Role Assigned</span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Status:</strong></div>
                                <div class="col-sm-8">
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning">Pending Verification</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Joined:</strong></div>
                                <div class="col-sm-8">{{ $user->created_at->format('M d, Y \a\t g:i A') }}</div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4"><strong>Last Update:</strong></div>
                                <div class="col-sm-8">{{ $user->updated_at->format('M d, Y \a\t g:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity and Permissions -->
                <div class="col-xl-8 col-lg-7">
                    <!-- Role Permissions Card -->
                    @if(!$user->is_super_admin)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Role Permissions</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $companyId = Auth::user()->is_super_admin 
                                    ? ($user->company_id ?? session('selected_company_id')) 
                                    : Auth::user()->company_id;
                                $userRole = $user->userRoles()->where('company_id', $companyId)->with('role')->first();
                            @endphp
                            @if($userRole && $userRole->role && $userRole->role->permissions)
                                <div class="row">
                                    @foreach($userRole->role->permissions as $permission)
                                        <div class="col-md-4 mb-2">
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-check text-success"></i> {{ $permission }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-user-times fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-muted">No role assigned or no permissions defined.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-danger">Super Admin Permissions</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-crown"></i> This user has <strong>full system access</strong> as a Super Administrator.
                                They can manage all companies, users, and system settings.
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Statistics Card -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">User Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="border-left-primary p-3">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Account Age</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            {{ $user->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border-left-success p-3">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Last Activity</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            {{ $user->updated_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection