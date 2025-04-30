@extends('layouts.app')

@section('title', 'Role Details')

@section('actions')
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Roles
    </a>
    <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
    </a>
    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash"></i> Delete
        </button>
    </form>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Role Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">ID</th>
                        <td>{{ $role->id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $role->name }}</td>
                    </tr>
                    <tr>
                        <th>Display Name</th>
                        <td>{{ $role->display_name }}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>{{ $role->description ?: 'No description provided' }}</td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $role->created_at->format('M d, Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $role->updated_at->format('M d, Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Assigned Permissions</h5>
            </div>
            <div class="card-body">
                @if($role->permissions->count() > 0)
                    @php
                        $groupedPermissions = $role->permissions->groupBy(function($permission) {
                            return explode('-', $permission->name)[0];
                        });
                    @endphp
                    
                    <div class="accordion" id="permissionsAccordion">
                        @foreach($groupedPermissions as $group => $permissions)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $group }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $group }}" aria-expanded="false" aria-controls="collapse{{ $group }}">
                                        {{ ucfirst($group) }} ({{ $permissions->count() }})
                                    </button>
                                </h2>
                                <div id="collapse{{ $group }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $group }}" data-bs-parent="#permissionsAccordion">
                                    <div class="accordion-body">
                                        <ul class="list-group list-group-flush">
                                            @foreach($permissions as $permission)
                                                <li class="list-group-item">
                                                    <strong>{{ $permission->display_name }}</strong>
                                                    <p class="text-muted mb-0 small">{{ $permission->description }}</p>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning">
                        This role has no permissions assigned.
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Users with this Role</h5>
            </div>
            <div class="card-body">
                @if($role->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        No users have been assigned this role.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
