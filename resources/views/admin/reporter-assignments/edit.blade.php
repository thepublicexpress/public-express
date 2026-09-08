@extends('layouts.admin')

@section('title', 'Edit Assignment')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-edit"></i> Edit Reporter Assignment</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reporter-assignments.update', $assignment) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Reporter -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Select Reporter <span class="text-danger">*</span></label>
                            <select name="reporter_id" class="form-select" required>
                                <option value="">-- Select Reporter --</option>
                                @foreach($reporters as $reporter)
                                    <option value="{{ $reporter->id }}" {{ old('reporter_id', $assignment->reporter_id) == $reporter->id ? 'selected' : '' }}>
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
                            <select name="assigned_state_id" class="form-select" id="stateSelect" onchange="loadDistricts(this.value)">
                                <option value="">-- Optional --</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" {{ old('assigned_state_id', $assignment->assigned_state_id) == $state->id ? 'selected' : '' }}>
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
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ old('assigned_district_id', $assignment->assigned_district_id) == $district->id ? 'selected' : '' }}>
                                        {{ $district->name_hi ?? $district->name }}
                                    </option>
                                @endforeach
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
                                @foreach($tehsils as $tehsil)
                                    <option value="{{ $tehsil->id }}" {{ old('assigned_tehsil_id', $assignment->assigned_tehsil_id) == $tehsil->id ? 'selected' : '' }}>
                                        {{ $tehsil->name_hi ?? $tehsil->name }}
                                    </option>
                                @endforeach
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
                                @foreach($blocks as $block)
                                    <option value="{{ $block->id }}" {{ old('assigned_block_id', $assignment->assigned_block_id) == $block->id ? 'selected' : '' }}>
                                        {{ $block->name_hi ?? $block->name }}
                                    </option>
                                @endforeach
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
                                    <option value="{{ $category->id }}" {{ old('assigned_category_id', $assignment->assigned_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name_hi ?? $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check mt-2">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" class="form-check-input" value="1" 
                                       {{ old('is_active', $assignment->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Multiple Categories – FIXED -->
                <div class="mb-3">
                    <label class="form-label">Assign Multiple Categories</label>
                    @php
                        // ✅ Safe handling: if it's a string, decode; otherwise use as is
                        $assignedCats = old('assigned_categories', $assignment->assigned_categories ?? []);
                        if (is_string($assignedCats)) {
                            $assignedCats = json_decode($assignedCats, true) ?? [];
                        }
                    @endphp
                    <div class="row">
                        @foreach($categories as $category)
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="assigned_categories[]" value="{{ $category->id }}" class="form-check-input"
                                           {{ (is_array($assignedCats) && in_array($category->id, $assignedCats)) ? 'checked' : '' }}>
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
                        <i class="fas fa-save"></i> Update Assignment
                    </button>
                    <a href="{{ route('admin.reporter-assignments.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Load districts based on selected state
    function loadDistricts(stateId) {
        const districtSelect = document.getElementById('districtSelect');
        const tehsilSelect = document.getElementById('tehsilSelect');
        const blockSelect = document.getElementById('blockSelect');
        
        if (!stateId) {
            districtSelect.innerHTML = '<option value="">-- Optional --</option>';
            tehsilSelect.innerHTML = '<option value="">-- Optional --</option>';
            blockSelect.innerHTML = '<option value="">-- Optional --</option>';
            return;
        }

        districtSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch(`/admin/api/districts/${stateId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Optional --</option>';
                data.forEach(d => {
                    const selected = d.id == {{ old('assigned_district_id', $assignment->assigned_district_id ?? 'null') }} ? 'selected' : '';
                    html += `<option value="${d.id}" ${selected}>${d.name}</option>`;
                });
                districtSelect.innerHTML = html;
                
                // Load tehsils if district is selected
                const currentDistrict = {{ old('assigned_district_id', $assignment->assigned_district_id ?? 'null') }};
                if (currentDistrict) {
                    loadTehsils(currentDistrict);
                }
            })
            .catch(() => {
                districtSelect.innerHTML = '<option value="">-- Error loading --</option>';
            });
    }

    // Load tehsils based on selected district
    function loadTehsils(districtId) {
        const tehsilSelect = document.getElementById('tehsilSelect');
        const blockSelect = document.getElementById('blockSelect');
        
        if (!districtId) {
            tehsilSelect.innerHTML = '<option value="">-- Optional --</option>';
            blockSelect.innerHTML = '<option value="">-- Optional --</option>';
            return;
        }

        tehsilSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch(`/admin/api/tehsils/${districtId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Optional --</option>';
                data.forEach(t => {
                    const selected = t.id == {{ old('assigned_tehsil_id', $assignment->assigned_tehsil_id ?? 'null') }} ? 'selected' : '';
                    html += `<option value="${t.id}" ${selected}>${t.name}</option>`;
                });
                tehsilSelect.innerHTML = html;
                
                // Load blocks if tehsil is selected
                const currentTehsil = {{ old('assigned_tehsil_id', $assignment->assigned_tehsil_id ?? 'null') }};
                if (currentTehsil) {
                    loadBlocks(currentTehsil);
                }
            })
            .catch(() => {
                tehsilSelect.innerHTML = '<option value="">-- Error loading --</option>';
            });
    }

    // Load blocks based on selected tehsil
    function loadBlocks(tehsilId) {
        const blockSelect = document.getElementById('blockSelect');
        
        if (!tehsilId) {
            blockSelect.innerHTML = '<option value="">-- Optional --</option>';
            return;
        }

        blockSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch(`/admin/api/blocks/${tehsilId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Optional --</option>';
                data.forEach(b => {
                    const selected = b.id == {{ old('assigned_block_id', $assignment->assigned_block_id ?? 'null') }} ? 'selected' : '';
                    html += `<option value="${b.id}" ${selected}>${b.name}</option>`;
                });
                blockSelect.innerHTML = html;
            })
            .catch(() => {
                blockSelect.innerHTML = '<option value="">-- Error loading --</option>';
            });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const stateSelect = document.getElementById('stateSelect');
        if (stateSelect.value) {
            loadDistricts(stateSelect.value);
        }
    });
</script>
@endsection