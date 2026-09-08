@extends('layouts.admin')
@section('title', 'Edit User - ' . $user->name)

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between mb-3">
        <h5>✏️ Edit User: {{ $user->name }}</h5>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- ✅ Form with enctype="multipart/form-data" for photo upload --}}
            <form action="{{ route('admin.users.update', $user) }}" method="POST" id="userForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Basic Info -->
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">📋 Basic Information</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                        </div>

                        {{-- ✅ Bio (जीवन परिचय) --}}
                        <div class="mb-3">
                            <label class="form-label">Bio / जीवन परिचय</label>
                            <textarea name="bio" class="form-control" rows="4">{{ old('bio', $user->bio) }}</textarea>
                        </div>

                        {{-- ✅ Profile Photo --}}
                        <div class="mb-3">
                            <label class="form-label">Profile Photo / प्रोफाइल फोटो</label>
                            <br>
                            @if($user->photo && file_exists(public_path($user->photo)))
                                <img src="{{ asset($user->photo) }}" width="100" height="100" style="border-radius:50%; object-fit:cover; margin-bottom:10px; border:2px solid #ddd;">
                                <br>
                            @else
                                <span class="text-muted">No photo uploaded</span>
                                <br>
                            @endif
                            <input type="file" name="photo" class="form-control-file" accept="image/*">
                            <small class="text-muted">Max: 2MB (jpg, png, gif)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">UPI ID (for withdrawals)</label>
                            <input type="text" name="upi_id" class="form-control" value="{{ old('upi_id', $user->upi_id) }}" placeholder="yourname@upi">
                        </div>
                    </div>

                    <!-- Role & Status -->
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">⚙️ Role & Status</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required id="roleSelect" onchange="toggleRoleFields(this.value)">
                                <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="state_admin" {{ old('role', $user->role) == 'state_admin' ? 'selected' : '' }}>State Admin</option>
                                <option value="district_admin" {{ old('role', $user->role) == 'district_admin' ? 'selected' : '' }}>District Admin</option>
                                <option value="tehsil_admin" {{ old('role', $user->role) == 'tehsil_admin' ? 'selected' : '' }}>Tehsil Admin</option>
                                <option value="block_admin" {{ old('role', $user->role) == 'block_admin' ? 'selected' : '' }}>Block Admin</option>
                                <option value="state_reporter" {{ old('role', $user->role) == 'state_reporter' ? 'selected' : '' }}>State Reporter</option>
                                <option value="district_reporter" {{ old('role', $user->role) == 'district_reporter' ? 'selected' : '' }}>District Reporter</option>
                                <option value="tehsil_reporter" {{ old('role', $user->role) == 'tehsil_reporter' ? 'selected' : '' }}>Tehsil Reporter</option>
                                <option value="block_reporter" {{ old('role', $user->role) == 'block_reporter' ? 'selected' : '' }}>Block Reporter</option>
                                <option value="national_reporter" {{ old('role', $user->role) == 'national_reporter' ? 'selected' : '' }}>National Reporter</option>
                                <option value="reporter" {{ old('role', $user->role) == 'reporter' ? 'selected' : '' }}>General Reporter</option>
                                <option value="subscriber" {{ old('role', $user->role) == 'subscriber' ? 'selected' : '' }}>Subscriber</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" value="1" 
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_approved" class="form-check-input" value="1" 
                                       {{ old('is_approved', $user->is_approved) ? 'checked' : '' }}>
                                <label class="form-check-label">Approved</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_verified" class="form-check-input" value="1" 
                                       {{ old('is_verified', $user->is_verified) ? 'checked' : '' }}>
                                <label class="form-check-label">Verified Reporter</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Assignment -->
                <div class="row mt-3" id="locationFields">
                    <div class="col-12">
                        <h6 class="fw-bold mb-3">📍 Location Assignment</h6>
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>For National Reporter:</strong> Leave all location fields empty.
                            <br>
                            <strong>For State/Reporter:</strong> Select the appropriate location level.
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">State</label>
                            <select name="assigned_state_id" class="form-select" id="stateSelect" onchange="loadDistricts(this.value)">
                                <option value="">-- Select State --</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" 
                                        {{ old('assigned_state_id', $user->assigned_state_id) == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select state for State Reporter</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">District</label>
                            <select name="assigned_district_id" class="form-select" id="districtSelect" onchange="loadTehsils(this.value)">
                                <option value="">-- Select District --</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" 
                                        {{ old('assigned_district_id', $user->assigned_district_id) == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select district for District Reporter</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Tehsil</label>
                            <select name="assigned_tehsil_id" class="form-select" id="tehsilSelect" onchange="loadBlocks(this.value)">
                                <option value="">-- Select Tehsil --</option>
                                @foreach($tehsils as $tehsil)
                                    <option value="{{ $tehsil->id }}" 
                                        {{ old('assigned_tehsil_id', $user->assigned_tehsil_id) == $tehsil->id ? 'selected' : '' }}>
                                        {{ $tehsil->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select tehsil for Tehsil Reporter</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Block</label>
                            <select name="assigned_block_id" class="form-select" id="blockSelect">
                                <option value="">-- Select Block --</option>
                                @foreach($blocks as $block)
                                    <option value="{{ $block->id }}" 
                                        {{ old('assigned_block_id', $user->assigned_block_id) == $block->id ? 'selected' : '' }}>
                                        {{ $block->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select block for Block Reporter</small>
                        </div>
                    </div>
                </div>

                <!-- Category Assignment -->
                <div class="row mt-2" id="categoryFields" style="display:none;">
                    <div class="col-12">
                        <h6 class="fw-bold mb-3">🏷️ Category Assignment</h6>
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Select ONE option below:</strong>
                            <br>
                            1. <strong>Single Category:</strong> Reporter can only post in one category.
                            <br>
                            2. <strong>Multiple Categories:</strong> Reporter can post in selected categories.
                            <br>
                            3. <strong>Leave both empty:</strong> Reporter can post in ALL categories.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Single Category</label>
                            <select name="assigned_category_id" class="form-select">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" 
                                        {{ old('assigned_category_id', $user->assigned_category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select ONE category (reporter can only post in this)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Multiple Categories</label>
                            <select name="assigned_categories[]" class="form-select" multiple style="height:120px;">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" 
                                        {{ is_array($assignedCategories ?? []) && in_array($cat->id, $assignedCategories ?? []) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl to select multiple categories</small>
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
                                       {{ old('can_approve', $user->can_approve) ? 'checked' : '' }}>
                                <label class="form-check-label">Can Approve News</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Approval Level</label>
                            <select name="approval_level" class="form-select">
                                <option value="none" {{ old('approval_level', $user->approval_level) == 'none' ? 'selected' : '' }}>None</option>
                                <option value="block" {{ old('approval_level', $user->approval_level) == 'block' ? 'selected' : '' }}>Block Level</option>
                                <option value="tehsil" {{ old('approval_level', $user->approval_level) == 'tehsil' ? 'selected' : '' }}>Tehsil Level</option>
                                <option value="district" {{ old('approval_level', $user->approval_level) == 'district' ? 'selected' : '' }}>District Level</option>
                                <option value="state" {{ old('approval_level', $user->approval_level) == 'state' ? 'selected' : '' }}>State Level</option>
                                <option value="national" {{ old('approval_level', $user->approval_level) == 'national' ? 'selected' : '' }}>National Level</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update User
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
    
    // Hide location fields for National Reporter
    if (role === 'national_reporter') {
        document.getElementById('locationFields').style.display = 'none';
    }
}

function loadDistricts(stateId) {
    const districtSelect = document.getElementById('districtSelect');
    const tehsilSelect = document.getElementById('tehsilSelect');
    const blockSelect = document.getElementById('blockSelect');
    
    if (!districtSelect) return;
    
    districtSelect.innerHTML = '<option value="">Loading...</option>';
    if (tehsilSelect) tehsilSelect.innerHTML = '<option value="">-- Select Tehsil --</option>';
    if (blockSelect) blockSelect.innerHTML = '<option value="">-- Select Block --</option>';
    
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
    
    if (!tehsilSelect) return;
    
    tehsilSelect.innerHTML = '<option value="">Loading...</option>';
    if (blockSelect) blockSelect.innerHTML = '<option value="">-- Select Block --</option>';
    
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
    
    if (!blockSelect) return;
    
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
    
    // Load districts if state is pre-selected
    const stateSelect = document.getElementById('stateSelect');
    if (stateSelect.value) {
        loadDistricts(stateSelect.value);
    }
    
    // Load tehsils if district is pre-selected
    const districtSelect = document.getElementById('districtSelect');
    if (districtSelect && districtSelect.value) {
        loadTehsils(districtSelect.value);
    }
    
    // Load blocks if tehsil is pre-selected
    const tehsilSelect = document.getElementById('tehsilSelect');
    if (tehsilSelect && tehsilSelect.value) {
        loadBlocks(tehsilSelect.value);
    }
});
</script>
@endsection