@extends('layouts.admin')

@section('title', 'Edit Ad - द पब्लिक एक्सप्रेस')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.ads.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">✏️ Edit Ad: {{ $ad->title }}</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <form action="{{ route('admin.ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ad Title *</label>
                    <input type="text" name="title" value="{{ old('title', $ad->title) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ad Type *</label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type', $ad->type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ad Position *</label>
                    <select name="position" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        @foreach($positions as $key => $label)
                            <option value="{{ $key }}" {{ old('position', $ad->position) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('position')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ad Image</label>
                    @if($ad->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" class="h-20 object-cover rounded-lg">
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image</p>
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- URL -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ad URL</label>
                    <input type="url" name="url" value="{{ old('url', $ad->url) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           placeholder="https://example.com">
                    @error('url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Code -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ad Code</label>
                    <textarea name="code" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">{{ old('code', $ad->code) }}</textarea>
                    @error('code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">{{ old('description', $ad->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location Targeting -->
                <div class="col-span-2">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">📍 Location Targeting</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">State</label>
                            <select name="state_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                                <option value="">All States</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" {{ old('state_id', $ad->state_id) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">District</label>
                            <select name="district_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                                <option value="">All Districts</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ old('district_id', $ad->district_id) == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Tehsil</label>
                            <select name="tehsil_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                                <option value="">All Tehsils</option>
                                @foreach($tehsils as $tehsil)
                                    <option value="{{ $tehsil->id }}" {{ old('tehsil_id', $ad->tehsil_id) == $tehsil->id ? 'selected' : '' }}>{{ $tehsil->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Block</label>
                            <select name="block_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                                <option value="">All Blocks</option>
                                @foreach($blocks as $block)
                                    <option value="{{ $block->id }}" {{ old('block_id', $ad->block_id) == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="col-span-2">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">📅 Schedule</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Start Date</label>
                            <input type="datetime-local" name="start_date" value="{{ old('start_date', $ad->start_date ? $ad->start_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">End Date</label>
                            <input type="datetime-local" name="end_date" value="{{ old('end_date', $ad->end_date ? $ad->end_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Status & Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="active" {{ old('status', $ad->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $ad->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="paused" {{ old('status', $ad->status) == 'paused' ? 'selected' : '' }}>Paused</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <input type="number" name="priority" value="{{ old('priority', $ad->priority) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           min="0" max="100">
                    <p class="text-xs text-gray-500 mt-1">Higher priority ads appear first (0-100)</p>
                    @error('priority')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg transition font-medium">
                    <i class="fas fa-save"></i> Update Ad
                </button>
                <a href="{{ route('admin.ads.index') }}" class="text-gray-600 hover:text-gray-900 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection