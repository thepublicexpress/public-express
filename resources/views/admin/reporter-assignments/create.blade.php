@extends('layouts.admin')

@section('title', 'Create Assignment')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-plus-circle"></i> New Reporter Assignment</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reporter-assignments.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Reporter -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Select Reporter <span class="text-danger">*</span></label>
                            <select name="reporter_id" class="form-select" required>
                                <option value="">-- Select Reporter --</option>
                                @foreach($reporters as $reporter)
                                    <option value="{{ $reporter->id }}" {{ old('reporter_id') == $reporter->id ? 'selected' : '' }}>
                                        {{ $reporter->name }} ({{ $reporter->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('reporter_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- State -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Assign State</label>
                            <select name="assigned_state_id" class="form-select" onchange="loadDistricts(this.value)">
                                <option value="">-- Optional --</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" {{ old('assigned_state_id') == $state->id ? 'selected' : '' }}>
                                        {{ $state->name_hi ?? $state->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_state_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- District -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Assign District</label>
                            <select name="assigned_district_id" id="districtSelect" class="form-select" onchange="loadTehsils(this.value)">
                                <option value="">-- Optional --</option>
                            </select>
                            @error('assigned_district_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Tehsil -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Assign Tehsil</label>
                            <select name="assigned_tehsil_id" id="tehsilSelect" class="form-select" onchange="loadBlocks(this.value)">
                                <option value="">-- Optional --</option>
                            </select>
                            @error('assigned_tehsil_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Block -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Assign Block</label>
                            <select name="assigned_block_id" id="blockSelect" class="form-select">
                                <option value="">-- Optional --</option>
                            </select>
                            @error('assigned_block_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Single Category -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Assign Category (Single)</label>
                            <select name="assigned_category_id" class="form-select">
                                <option value="">-- Optional --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('assigned_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name_hi ?? $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Multiple Categories -->
                <div class="mb-3">
                    <label class="form-label">Assign Multiple Categories</label>
                    <div class="row">
                        @foreach($categories as $category)
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="assigned_categories[]" value="{{ $category->id }}" class="form-check-input"
                                           {{ (old('assigned_categories') && in_array($category->id, old('assigned_categories'))) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $category->name_hi ?? $category->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <small class="text-muted">Select multiple categories for the reporter.</small>
                    @error('assigned_categories')
                        <br><small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Assign Reporter
                    </button>
                    <a href="{{ route('admin.reporter-assignments.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function loadDistricts(stateId) {
        if (!stateId) {
            document.getElementById('districtSelect').innerHTML = '<option value="">-- Optional --</option>';
            document.getElementById('tehsilSelect').innerHTML = '<option value="">-- Optional --</option>';
            document.getElementById('blockSelect').innerHTML = '<option value="">-- Optional --</option>';
            return;
        }

        fetch(`/admin/api/districts/${stateId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Optional --</option>';
                data.forEach(d => {
                    html += `<option value="${d.id}">${d.name}</option>`;
                });
                document.getElementById('districtSelect').innerHTML = html;
            });
    }

    function loadTehsils(districtId) {
        if (!districtId) {
            document.getElementById('tehsilSelect').innerHTML = '<option value="">-- Optional --</option>';
            document.getElementById('blockSelect').innerHTML = '<option value="">-- Optional --</option>';
            return;
        }

        fetch(`/admin/api/tehsils/${districtId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Optional --</option>';
                data.forEach(t => {
                    html += `<option value="${t.id}">${t.name}</option>`;
                });
                document.getElementById('tehsilSelect').innerHTML = html;
            });
    }

    function loadBlocks(tehsilId) {
        if (!tehsilId) {
            document.getElementById('blockSelect').innerHTML = '<option value="">-- Optional --</option>';
            return;
        }

        fetch(`/admin/api/blocks/${tehsilId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Optional --</option>';
                data.forEach(b => {
                    html += `<option value="${b.id}">${b.name}</option>`;
                });
                document.getElementById('blockSelect').innerHTML = html;
            });
    }
</script>
@endsection