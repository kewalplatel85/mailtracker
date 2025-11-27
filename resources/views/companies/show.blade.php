@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">{{ $company->name }}</h1>
                <div>
                    <a href="{{ route('companies.edit', $company) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Company
                    </a>
                    <a href="{{ route('companies.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Companies
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Company Info Card -->
                <div class="col-xl-4 col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Company Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Name:</strong></div>
                                <div class="col-sm-8">{{ $company->name }}</div>
                            </div>
                            
                            @if($company->email)
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Email:</strong></div>
                                <div class="col-sm-8">
                                    <a href="mailto:{{ $company->email }}">{{ $company->email }}</a>
                                </div>
                            </div>
                            @endif

                            @if($company->phone)
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Phone:</strong></div>
                                <div class="col-sm-8">{{ $company->phone }}</div>
                            </div>
                            @endif

                            @if($company->website)
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Website:</strong></div>
                                <div class="col-sm-8">
                                    <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Status:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge bg-{{ $company->status == 'active' ? 'success' : ($company->status == 'suspended' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($company->status) }}
                                    </span>
                                </div>
                            </div>

                            @if($company->timezone)
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Timezone:</strong></div>
                                <div class="col-sm-8">{{ $company->timezone }}</div>
                            </div>
                            @endif

                            @if($company->address)
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Address:</strong></div>
                                <div class="col-sm-8">{{ $company->address }}</div>
                            </div>
                            @endif

                            @if($company->description)
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Description:</strong></div>
                                <div class="col-sm-8">{{ $company->description }}</div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Created:</strong></div>
                                <div class="col-sm-8">{{ $company->created_at->format('M d, Y \a\t g:i A') }}</div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4"><strong>Updated:</strong></div>
                                <div class="col-sm-8">{{ $company->updated_at->format('M d, Y \a\t g:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics and Data -->
                <div class="col-xl-8 col-lg-7">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $company->users->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-success">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Packages</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $company->packages->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-box fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-info">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Active Roles</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $company->roles->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-tag fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Company Users</h6>
                        </div>
                        <div class="card-body">
                            @if($company->users->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Joined</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($company->users as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @php
                                                        $userRole = $user->userRoles()->where('company_id', $company->id)->with('role')->first();
                                                    @endphp
                                                    @if($userRole && $userRole->role)
                                                        <span class="badge bg-primary">{{ $userRole->role->name }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">No Role</span>
                                                    @endif
                                                </td>
                                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-muted">No users found for this company.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Packages -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Packages (Last 10)</h6>
                        </div>
                        <div class="card-body">
                            @if($company->packages->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tracking Number</th>
                                                <th>Recipient</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($company->packages->take(10) as $package)
                                            <tr>
                                                <td>{{ $package->tracking_number }}</td>
                                                <td>{{ $package->recipient_name }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $package->status == 'delivered' ? 'success' : ($package->status == 'shipped' ? 'primary' : 'warning') }}">
                                                        {{ ucfirst($package->status ?? 'pending') }}
                                                    </span>
                                                </td>
                                                <td>{{ $package->created_at->format('M d, Y') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-box fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-muted">No packages found for this company.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection