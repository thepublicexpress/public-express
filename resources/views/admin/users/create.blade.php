@extends('layouts.admin')
@section('title', 'Create User')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between mb-3">
        <h5>👤 Create New User</h5>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST" id="userForm">
                @csrf

                <div class="row">
                    <!-- Basic Info -->
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">📋 Basic Information</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">UPI ID (for withdrawals)</label>
                            <input type="text" name="upi_id" class="form-control" value="{{ old('upi_id') }}" placeholder="yourname@upi">
                        </div>
                    </div>

                    <!-- Role & Status -->
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">⚙️ Role & Status</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required id="roleSelect" onchange="toggleRoleFields(this.value)">
                                <option value="">-- Select Role --</option>
                                <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="state_admin" {{ old('role') == 'state_admin' ? 'selected' : '' }}>State Admin</option>
                                <option value="district_admin" {{ old('role') == 'district_admin' ? 'selected' : '' }}>District Admin</option>
                                <option value="tehsil_admin" {{ old('role') == 'tehsil_admin' ? 'selected' : '' }}>Tehsil Admin</option>
                                <option value="block_admin" {{ old('role') == 'block_admin' ? 'selected' : '' }}>Block Admin</option>
                                <option value="state_reporter" {{ old('role') == 'state_reporter' ? 'selected' : '' }}>State Reporter</option>
                                <option value="district_reporter" {{ old('role') == 'district_reporter' ? 'selected' : '' }}>District Reporter</option>
                                <option value="tehsil_reporter" {{ old('role') == 'tehsil_reporter' ? 'selected' : '' }}>Tehsil Reporter</option>
                                <option value="block_reporter" {{ old('role') == 'block_reporter' ? 'selected' : '' }}>Block Reporter</option>
                                <option value="national_reporter" {{ old('role') == 'national_reporter' ? 'selected' : '' }}>National Reporter</option>
                                <option value="reporter" {{ old('role') == 'reporter' ? 'selected' : '' }}>General Reporter</option>
                                <option value="subscriber" {{ old('role') == 'subscriber' ? 'selected' : '' }}>Subscriber</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_approved" class="form-check-input" value="1">
                                <label class="form-check-label">Approved</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_verified" class="form-check-input" value="1">
                                <label class="form-check-label">Verified Reporter</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Assignment -->
                <div class="row mt-3" id="locationFields">
                    <div class="col-12">
                        <h6 class="fw-bold mb-3">📍 Location Assignment</h6>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">State</label>
                            <select name="assigned_state_id" class="form-select" id="stateSelect" onchange="loadDistricts(this.value)">
                                <option value="">-- Select State --</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" {{ old('assigned_state_id') == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">District</label>
                            <select name="assigned_district_id" class="form-select" id="districtSelect" onchange="loadTehsils(this.value)">
                                <option value="">-- Select District --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Tehsil</label>
                            <select name="assigned_tehsil_id" class="form-select" id="tehsilSelect" onchange="loadBlocks(this.value)">
                                <option value="">-- Select Tehsil --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Block</label>
                            <select name="assigned_block_id" class="form-select" id="blockSelect">
                                <option value="">-- Select Block --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Category Assignment -->
                <div class="row mt-2" id="categoryFields" style="display:none;">
                    <div class="col-12">
                        <h6 class="fw-bold mb-3">🏷️ Category Assignment</h6>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Single Category</label>
                            <select name="assigned_category_id" class="form-select">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('assigned_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Multiple Categories</label>
                            <select name="assigned_categories[]" class="form-select" multiple style="height:100px;">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" 
                                        {{ is_array(old('assigned_categories')) && in_array($cat->id, old('assigned_categories')) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl to select multiple</small>
                        </div>
                    </div>
                </div>

                <!-- Approval Settings -->
                <div class="row mt-2" id="approvalFields" style="display:none;">
                    <div class="col-12">
                        <h6 class="fw-bold mb-3">✅ Approval Settings</h6>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="can_approve" class="form-check-input" value="1" 
                                       {{ old('can_approve') ? 'checked' : '' }}>
                                <label class="form-check-label">Can Approve News</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Approval Level</label>
                            <select name="approval_level" class="form-select">
                                <option value="none" {{ old('approval_level') == 'none' ? 'selected' : '' }}>None</option>
                                <option value="block" {{ old('approval_level') == 'block' ? 'selected' : '' }}>Block Level</option>
                                <option value="tehsil" {{ old('approval_level') == 'tehsil' ? 'selected' : '' }}>Tehsil Level</option>
                                <option value="district" {{ old('approval_level') == 'district' ? 'selected' : '' }}>District Level</option>
                                <option value="state" {{ old('approval_level') == 'state' ? 'selected' : '' }}>State Level</option>
                                <option value="national" {{ old('approval_level') == 'national' ? 'selected' : '' }}>National Level</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create User
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleRoleFields(role) {
    const isAdmin = ['super_admin', 'admin', 'state_admin', 'district_admin', 'tehsil_admin', 'block_admin'].includes(role);
    const isReporter = ['reporter', 'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter'].includes(role);
    
    document.getElementById('locationFields').style.display = (isAdmin || isReporter) ? 'flex' : 'none';
    document.getElementById('categoryFields').style.display = isReporter ? 'flex' : 'none';
    document.getElementById('approvalFields').style.display = isAdmin ? 'flex' : 'none';
}

function loadDistricts(stateId) {
    const districtSelect = document.getElementById('districtSelect');
    const tehsilSelect = document.getElementById('tehsilSelect');
    const blockSelect = document.getElementById('blockSelect');
    
    districtSelect.innerHTML = '<option value="">Loading...</option>';
    tehsilSelect.innerHTML = '<option value="">-- Select Tehsil --</option>';
    blockSelect.innerHTML = '<option value="">-- Select Block --</option>';
    
    if (!stateId) {
        districtSelect.innerHTML = '<option value="">-- Select District --</option>';
        return;
    }
    
    fetch('/api/get-districts/' + stateId)
        .then(r => r.json())
        .then(data => {
            districtSelect.innerHTML = '<option value="">-- Select District --</option>';
            data.forEach(d => {
                districtSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
            });
        });
}

function loadTehsils(districtId) {
    const tehsilSelect = document.getElementById('tehsilSelect');
    const blockSelect = document.getElementById('blockSelect');
    
    tehsilSelect.innerHTML = '<option value="">Loading...</option>';
    blockSelect.innerHTML = '<option value="">-- Select Block --</option>';
    
    if (!districtId) {
        tehsilSelect.innerHTML = '<option value="">-- Select Tehsil --</option>';
        return;
    }
    
    fetch('/api/get-tehsils/' + districtId)
        .then(r => r.json())
        .then(data => {
            tehsilSelect.innerHTML = '<option value="">-- Select Tehsil --</option>';
            data.forEach(t => {
                tehsilSelect.innerHTML += `<option value="${t.id}">${t.name}</option>`;
            });
        });
}

function loadBlocks(tehsilId) {
    const blockSelect = document.getElementById('blockSelect');
    
    blockSelect.innerHTML = '<option value="">Loading...</option>';
    
    if (!tehsilId) {
        blockSelect.innerHTML = '<option value="">-- Select Block --</option>';
        return;
    }
    
    fetch('/api/get-blocks/' + tehsilId)
        .then(r => r.json())
        .then(data => {
            blockSelect.innerHTML = '<option value="">-- Select Block --</option>';
            data.forEach(b => {
                blockSelect.innerHTML += `<option value="${b.id}">${b.name}</option>`;
            });
        });
}

// Load initial data if role is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('roleSelect');
    if (roleSelect.value) {
        toggleRoleFields(roleSelect.value);
    }
});
</script>
@endsection