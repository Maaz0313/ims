@extends('layouts.app')

@section('title', 'Create Role')

@section('actions')
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Roles
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Create New Role</h5>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3">Role Information</h6>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                            value="{{ old('name') }}" required>
                        <small class="text-muted">Unique identifier (e.g., admin, manager, staff)</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('display_name') is-invalid @enderror" id="display_name" name="display_name"
                            value="{{ old('display_name') }}" required>
                        <small class="text-muted">Human-readable name (e.g., Administrator, Manager, Staff)</small>
                        @error('display_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        <small class="text-muted">Brief description of the role's purpose</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="mb-3">Permissions</h6>
                    
                    <div class="mb-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="select-all">
                            <label class="form-check-label fw-bold" for="select-all">
                                Select All Permissions
                            </label>
                        </div>
                        
                        <div class="border rounded p-3" style="max-height: 350px; overflow-y: auto;">
                            <div class="row">
                                @php
                                    $groupedPermissions = $permissions->groupBy(function($permission) {
                                        return explode('-', $permission->name)[0];
                                    });
                                @endphp
                                
                                @foreach($groupedPermissions as $group => $items)
                                    <div class="col-md-12 mb-3">
                                        <h6 class="border-bottom pb-2">{{ ucfirst($group) }}</h6>
                                        
                                        @foreach($items as $permission)
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                    id="permission-{{ $permission->id }}" 
                                                    name="permissions[]" 
                                                    value="{{ $permission->id }}"
                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                    {{ $permission->display_name }}
                                                    <small class="text-muted d-block">{{ $permission->description }}</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @error('permissions')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkbox functionality
        const selectAllCheckbox = document.getElementById('select-all');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
        
        selectAllCheckbox.addEventListener('change', function() {
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
        
        // Update "select all" checkbox state based on individual checkboxes
        function updateSelectAllCheckbox() {
            const allChecked = Array.from(permissionCheckboxes).every(checkbox => checkbox.checked);
            const someChecked = Array.from(permissionCheckboxes).some(checkbox => checkbox.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        }
        
        permissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectAllCheckbox);
        });
        
        // Initial state
        updateSelectAllCheckbox();
    });
</script>
@endsection
