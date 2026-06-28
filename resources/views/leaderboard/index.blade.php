@extends('layouts.app')

@section('title', 'लीडरबोर्ड')

@section('content')
<div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">लीडरबोर्ड</h1>
            <p class="mt-1 text-sm text-slate-500">सर्वश्रेष्ठ रिपोर्टर और उनके पॉइंट्स</p>
        </div>
        <div class="rounded-full bg-brand/10 px-3 py-1 text-sm font-semibold text-brand">Top 10</div>
    </div>

    <div class="space-y-3">
        @forelse($reporters as $index => $reporter)
            <div class="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand font-bold text-white">
                        {{ $index + 1 }}
                    </div>
                    <div>
                        <div class="font-semibold text-slate-900">{{ $reporter->name ?: 'अज्ञात रिपोर्टर' }}</div>
                        <div class="text-sm text-slate-500">{{ $reporter->district?->name ?? '—' }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-bold text-brand">{{ $reporter->points }} पॉइंट्स</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">रिपोर्टर</div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-slate-500">
                अभी तक कोई रिपोर्टर उपलब्ध नहीं है।
            </div>
        @endforelse
    </div>
</div>
@endsection
