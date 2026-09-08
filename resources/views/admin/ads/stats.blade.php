@extends('layouts.admin')

@section('title', 'Ad Stats - द पब्लिक एक्सप्रेस')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.ads.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">📊 Ad Statistics: {{ $ad->title }}</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Impressions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($ad->impressions ?? 0) }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-eye text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Clicks</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($ad->clicks ?? 0) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-mouse-pointer text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">CTR (Click-Through Rate)</p>
                    <p class="text-2xl font-bold text-gray-900">
                        @if($ad->impressions > 0)
                            {{ round(($ad->clicks / $ad->impressions) * 100, 2) }}%
                        @else
                            0%
                        @endif
                    </p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ad Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Title</p>
                <p class="font-medium">{{ $ad->title }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Type</p>
                <p class="font-medium">{{ ucfirst($ad->type) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Position</p>
                <p class="font-medium capitalize">{{ str_replace('-', ' ', $ad->position) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <span class="px-2 py-1 text-xs rounded-full {{ $ad->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($ad->status) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Location</p>
                <p class="font-medium">
                    @if($ad->state)
                        {{ $ad->state->name }}
                    @elseif($ad->district)
                        {{ $ad->district->name }}
                    @elseif($ad->tehsil)
                        {{ $ad->tehsil->name }}
                    @elseif($ad->block)
                        {{ $ad->block->name }}
                    @else
                        All Locations
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Created</p>
                <p class="font-medium">{{ $ad->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>

        @if($ad->image)
            <div class="mt-4">
                <p class="text-sm text-gray-500 mb-2">Ad Preview</p>
                <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" class="max-h-48 rounded-lg border border-gray-200">
            </div>
        @endif
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.ads.edit', $ad->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition inline-flex items-center gap-2">
            <i class="fas fa-edit"></i> Edit Ad
        </a>
    </div>
</div>
@endsection