@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">User Management</h1>
                @can('users.create')
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New User
                </a>
                @endcan
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    @if(Auth::user()->is_super_admin)
                                        <th>Company</th>
                                    @endif
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        @if($user->email_verified_at)
                                            <small class="text-success">
                                                <i class="fas fa-check-circle"></i> Verified
                                            </small>
                                        @else
                                            <small class="text-warning">
                                                <i class="fas fa-exclamation-circle"></i> Unverified
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    @if(Auth::user()->is_super_admin)
                                        <td>
                                            @if($user->company)
                                                <span class="badge bg-info">{{ $user->company->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">No Company</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td>
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
                                            @else
                                                <span class="badge bg-secondary">No Role</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('users.edit')
                                                <a href="{{ route('users.show', $user) }}"
                                                   class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                        title="Edit Role" onclick="editUserRole({{ $user->id }})">
                                                    <i class="fas fa-user-tag"></i>
                                                </button>
                                            @endcan
                                            @can('users.delete')
                                                @if(!$user->is_super_admin && $user->id !== Auth::id())
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        title="Delete" onclick="confirmDelete({{ $user->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->is_super_admin ? 8 : 7 }}" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-2x mb-3"></i>
                                            <p>No users found. <a href="{{ route('users.create') }}">Create the first user</a>.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Role Assignment Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalLabel">Assign Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="roleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="roleSelect" class="form-label">Select Role</label>
                        <select class="form-select" id="roleSelect" name="role_id" required>
                            <option value="">Choose a role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">
                            This will assign the selected role to the user for the current company context.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this user? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editUserRole(userId) {
    const roleForm = document.getElementById('roleForm');
    roleForm.action = `/users/${userId}/assign-role`;
    const roleModal = new bootstrap.Modal(document.getElementById('roleModal'));
    roleModal.show();
}

function confirmDelete(userId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/users/${userId}`;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection
