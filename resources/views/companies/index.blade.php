@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Company Management</h1>
                <a href="{{ route('companies.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Company
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Companies</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Company Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Users</th>
                                    <th>Packages</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($companies as $company)
                                <tr>
                                    <td>{{ $company->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $company->name }}</div>
                                        @if($company->address)
                                            <small class="text-muted">{{ $company->address }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $company->email ?? '-' }}</td>
                                    <td>{{ $company->phone ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $company->users_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $company->packages_count }}</span>
                                    </td>
                                    <td>{{ $company->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('companies.show', $company) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('companies.edit', $company) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    title="Delete" onclick="confirmDelete({{ $company->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-building fa-2x mb-3"></i>
                                            <p>No companies found. <a href="{{ route('companies.create') }}">Create the first company</a>.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($companies->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $companies->links() }}
                        </div>
                    @endif
                </div>
            </div>
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
                Are you sure you want to delete this company? This action cannot be undone and will also remove all associated users and packages.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Company</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(companyId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/companies/${companyId}`;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection